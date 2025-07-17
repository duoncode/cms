<?php

declare(strict_types=1);

namespace Duon\Cms\Node;

use Duon\Cms\Config;
use Duon\Cms\Context;
use Duon\Cms\Exception\NoSuchField;
use Duon\Cms\Exception\RuntimeException;
use Duon\Cms\Field\Attr;
use Duon\Cms\Field\Field;
use Duon\Cms\Finder\Finder;
use Duon\Cms\Locale;
use Duon\Cms\Schema\NodeSchemaFactory;
use Duon\Cms\Value\Value;
use Duon\Cms\Value\ValueContext;
use Duon\Core\Exception\HttpBadRequest;
use Duon\Core\Factory;
use Duon\Core\Request;
use Duon\Core\Response;
use Duon\Quma\Database;
use Duon\Registry\Registry;
use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;
use Throwable;

use function Duon\Cms\Util\nanoid;

abstract class Node
{
	public readonly Request $request;
	public readonly Config $config;
	protected readonly Database $db;
	protected readonly Registry $registry;
	protected readonly Factory $factory;
	protected array $fieldNames = [];

	/** @var array<class-string, Meta> */
	protected static array $nodeMeta = [];

	final public function __construct(
		public readonly Context $context,
		protected readonly Finder $find,
		protected ?array $data = null,
	) {
		static::initMeta();
		$this->initFields();

		$this->db = $context->db;
		$this->request = $context->request;
		$this->config = $context->config;
		$this->registry = $context->registry;
		$this->factory = $context->factory;
	}

	protected static function initMeta()
	{
		if (!isset(self::$nodeMeta[static::class])) {
			self::$nodeMeta[static::class] = new Meta(static::class);
		}
	}

	public static function name(): string
	{
		static::initMeta();

		return self::$nodeMeta[static::class]->name;
	}

	public static function handle(): string
	{
		static::initMeta();

		return self::$nodeMeta[static::class]->handle;
	}

	public static function permission(): array
	{
		static::initMeta();

		return self::$nodeMeta[static::class]->permission;
	}

	public static function route(): string
	{
		static::initMeta();

		return self::$nodeMeta[static::class]->route;
	}

	final public function __get(string $fieldName): ?Value
	{
		return $this->getValue($fieldName);
	}

	// TODO: should be optimized as this could result
	//       in many ::get() calls
	final public function __isset(string $fieldName): bool
	{
		if (isset($this->{$fieldName})) {
			return $this->{$fieldName}->value()->isset();
		}

		return false;
	}

	final public function setData(array $data): static
	{
		$this->data = $data;
		$this->initFields();

		return $this;
	}

	final public function getValue(string $fieldName): ?Value
	{
		if (!property_exists($this, $fieldName)) {
			$type = $this::class;

			throw new NoSuchField("The field '{$fieldName}' does not exist on node with type '{$type}'.");
		}

		$field = $this->{$fieldName};
		$value = $field->value();

		if ($value->isset()) {
			return $value;
		}

		return null;
	}

	final public function getField(string $fieldName): Field
	{
		return $this->{$fieldName};
	}

	public function meta(string $fieldName): mixed
	{
		return $this->data[$fieldName];
	}

	public function content(): array
	{
		$content = [];

		// Fill the field's value with missing keys from the structure and fix type
		foreach ($this->fieldNames as $fieldName) {
			$field = $this->{$fieldName};
			$structure = $field->structure();
			$content[$fieldName] = array_merge($structure, $this->data['content'][$fieldName] ?? []);
			$content[$fieldName]['type'] = $structure['type'];
		}

		return $content;
	}

	public function data(): array
	{
		$data = $this->data;
		$result = [
			'uid' => $data['uid'],
			'published' => $data['published'],
			'hidden' => $data['hidden'],
			'locked' => $data['locked'],
			'created' => $data['created'],
			'changed' => $data['changed'],
			'deleted' => $data['deleted'],
			'paths' => $data['paths'],
			'type' => [
				'handle' => $data['handle'],
				'kind' => $data['kind'],
				'class' => $this::class,
			],
			'editor' => [
				'uid' => $data['editor_uid'],
				'email' => $data['editor_email'],
				'username' => $data['editor_username'],
				'data' => $data['editor_data'],
			],
			'creator' => [
				'uid' => $data['creator_uid'],
				'email' => $data['creator_email'],
				'username' => $data['creator_username'],
				'data' => $data['creator_data'],
			],
			'content' => $this->content(),
			'deletable' => $this->deletable(),
		];

		return $result;
	}

	public function blueprint(array $values = []): array
	{
		$content = [];
		$paths = [];

		foreach ($this->fieldNames as $fieldName) {
			$field = $this->{$fieldName};
			$content[$fieldName] = $field->structure($values[$fieldName] ?? null);
		}

		// TODO: Improve the node kind determination or get rid of it
		$kind = match (true) {
			is_subclass_of($this, Page::class) => 'page',
			is_subclass_of($this, Block::class) => 'block',
			default => 'document',
		};

		foreach ($this->context->locales() as $locale) {
			$paths[$locale->id] = '';
		}

		return [
			'title' => _('Neues Dokument:') . ' ' . static::$nodeMeta->name,
			'fields' => $this->fields(),
			'uid' => $this->newUid(),
			'published' => false,
			'hidden' => false,
			'locked' => false,
			'deletable' => $this->deletable(),
			'content' => $content,
			'type' => [
				'handle' => static::$nodeMeta->handle,
				'kind' => $kind,
				'class' => static::class,
			],
			'paths' => $paths,
			'generatedPaths' => [],
		];
	}

	protected function newUid(): string
	{
		return nanoid();
	}

	public function fillData(array $data): array
	{
		return $this->blueprint($data);
	}

	/**
	 * Is called after self::initFields.
	 *
	 * Can be used to make adjustments the already initialized fields
	 */
	public function init(): void {}

	public static function className(): string
	{
		return basename(str_replace('\\', '/', static::class));
	}

	/**
	 * Should return the general title of the node.
	 *
	 * Shown in the admin interface. But can also be used in the frontend.
	 */
	abstract public function title(): string;

	public function uid(): string
	{
		return $this->data['uid'];
	}

	public function order(): array
	{
		return $this->fieldNames;
	}

	public function fieldNames(): array
	{
		return $this->fieldNames;
	}

	public function fields(): array
	{
		$fields = [];
		$orderedFields = $this->order();
		$missingFields = array_diff($this->fieldNames, $orderedFields);
		$allFields = array_merge($orderedFields, $missingFields);

		foreach ($allFields as $fieldName) {
			$fields[] = $this->{$fieldName}->properties();
		}

		return $fields;
	}

	public function response(): Response
	{
		$request = $this->request;

		return match ($request->method()) {
			'GET' => $this->render(),
			'POST' => $this->formPost($request->form()),
			default => throw new HttpBadRequest($request),
		};
	}

	public function jsonResponse(): Response
	{
		$request = $this->request;
		$method = $request->method();

		$content = json_encode(match ($method) {
			'GET' => $this->read(),
			'POST' => $this->create(),
			'PUT' => $this->change(),
			'DELETE' => $this->delete(),
			default => throw new HttpBadRequest($request),
		}, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
		$response = (
			new Response(
				$this->factory
					->response()
					->withStatus($method === 'POST' ? 201 : 200)
					->withHeader('Content-Type', 'application/json'),
			)
		)->body($content);

		return $response;
	}

	public function render(array $context = []): Response
	{
		return (new Response($this->factory
			->response()
			->withHeader('Content-Type', 'application/json')))->body(
				json_encode(
					$this->read(),
					JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
				),
			);
	}

	/**
	 * Called on GET request.
	 */
	public function read(): array
	{
		$data = $this->data();

		return array_merge([
			'title' => $this->title(),
			'uid' => $this->meta('uid'),
			'fields' => $this->fields(),
		], $data);
	}

	/**
	 * Called on PUT request.
	 */
	public function change(): array
	{
		return $this->save($this->getRequestData());
	}

	/**
	 * Called on DELETE request.
	 */
	public function delete(): array
	{
		if ($this->request->header('Accept') !== 'application/json') {
			throw new HttpBadRequest($this->request);
		}

		$this->db->nodes->delete([
			'uid' => $this->uid(),
			'editor' => $this->request->get('session')->authenticatedUserId(),
		])->run();

		return [
			'success' => true,
			'error' => false,
		];
	}

	/**
	 * Called on POST request.
	 */
	public function create(): array|Response
	{
		$data = $this->getRequestData();
		$node = $this->db->nodes->find(['uid' => $data['uid']])->one();

		if ($node) {
			throw new HttpBadRequest($this->request, payload: [
				'message' => _('A node with the same uid already exists: ') . $data['uid'],
			]);
		}

		return $this->save($data);
	}

	/**
	 * Validates the data and persists it in the database.
	 */
	public function save(array $data): array
	{
		$data = $this->validate($data);

		if ($data['locked']) {
			throw new HttpBadRequest($this->request, payload: ['message' => _('This document is locked')]);
		}

		// TODO: check permissions
		try {
			$editor = $this->request->get('session')->authenticatedUserId();

			if (!$editor) {
				$editor = 1;
			}
		} catch (Throwable) {
			$editor = 1; // The System user
		}

		try {
			$db = $this->db;
			$db->begin();

			$this->persist($db, $data, $editor);

			$db->commit();
		} catch (Throwable $e) {
			$db->rollback();

			throw new RuntimeException(
				_('Fehler beim Speichern: ') . $e->getMessage(),
				(int) $e->getCode(),
				previous: $e,
			);
		}

		return [
			'success' => true,
			'uid' => $data['uid'],
		];
	}

	protected function persist(Database $db, array $data, int $editor): void
	{
		$this->persistNode($db, $data, $editor);
	}

	protected function persistNode(Database $db, array $data, int $editor): int
	{
		return (int) $db->nodes->save([
			'uid' => $data['uid'],
			'hidden' => $data['hidden'],
			'published' => $data['published'],
			'locked' => $data['locked'],
			'type' => static::$nodeMeta->handle,
			'content' => json_encode($data['content']),
			'editor' => $editor,
		])->one()['node'];
	}

	protected function validate(array $data): array
	{
		$factory = new NodeSchemaFactory($this, $this->context->locales());
		$schema = $factory->create();

		if (!$schema->validate($data)) {
			throw new HttpBadRequest($this->request, payload: [
				'message' => _('Incomplete or invalid data'),
				'errors' => $schema->errors(),
			]);
		}

		return $schema->values();
	}

	protected function getRequestData(): array
	{
		if ($this->request->header('Content-Type') !== 'application/json') {
			throw new HttpBadRequest($this->request);
		}

		return $this->request->json();
	}

	/**
	 * Usually overwritten in the app. Used to handle form posts
	 * from the frontend.
	 */
	protected function formPost(?array $body): Response
	{
		throw new HttpBadRequest($this->request);
	}

	protected function locale(): Locale
	{
		return $this->request->get('locale');
	}

	protected function getResponse(): Response
	{
		$factory = $this->registry->get(Factory::class);

		return Response::create($factory);
	}

	protected function initFields(): void
	{
		$this->fieldNames = [];

		$rc = new ReflectionClass(static::class);

		foreach ($rc->getProperties() as $property) {
			$name = $property->getName();

			if (!$property->hasType()) {
				continue;
			}

			$fieldType = $property->getType();

			if ($fieldType::class === ReflectionUnionType::class) {
				continue;
			}

			$fieldTypeName = $fieldType->getName();

			if (is_subclass_of($fieldTypeName, Field::class)) {
				if (isset($this->{$name})) {
					continue;
				}

				$this->{$name} = $this->initField($property, $fieldTypeName);

				$this->fieldNames[] = $name;
			}
		}

		$this->init();
	}

	protected function initField(ReflectionProperty $property, string $fieldType): Field
	{
		$fieldName = $property->getName();
		$content = $this->data['content'][$fieldName] ?? [];
		$node = $this instanceof Node ? $this : $this->node;
		$field = new $fieldType($fieldName, $node, new ValueContext($fieldName, $content));

		foreach ($property->getAttributes() as $attr) {
			switch ($attr->getName()) {
				case Attr\Required::class:
					$field->required(true);
					break;
				case Attr\Immutable::class:
					$field->immutable(true);
					break;
				case Attr\Hidden::class:
					$field->hidden(true);
					break;
				case Attr\Translate::class:
					$field->translate(true);
					break;
				case Attr\Label::class:
					$field->label($attr->newInstance()->label);
					break;
				case Attr\Description::class:
					$field->description($attr->newInstance()->description);
					break;
				case Attr\Fulltext::class:
					$field->fulltext($attr->newInstance()->fulltextWeight);
					break;
				case Attr\Width::class:
					$field->width($attr->newInstance()->width);
					break;
				case Attr\Rows::class:
					$field->rows($attr->newInstance()->rows);
					break;
				case Attr\Multiple::class:
					$field->multiple(true);
					break;
				case Attr\Validate::class:
					$field->validate(...$attr->newInstance()->validators);
					break;
				case Attr\Options::class:
					$field->options($attr->newInstance()->options);
					break;
				case Attr\DefaultVal::class:
					$field->default($attr->newInstance()->get());
					break;
				case Attr\TranslateFile::class:
					if (!($field instanceof \Duon\Cms\Field\Image) && !($field instanceof \Duon\Cms\Field\File)) {
						throw new RuntimeException(
							'Cannot apply attribute TranslateFile to field ' . $fieldName . ' of type ' . $field::class,
						);
					}

					$field->translateFile(true);

					break;
				case Attr\Columns::class:
					if (!$field instanceof \Duon\Cms\Field\Grid) {
						throw new RuntimeException('Cannot apply attribute Columns to ' . $field::class);
					}

					$instance = $attr->newInstance();
					$field->columns($instance->columns, $instance->minCellWidth);

					break;
			}
		}

		return $field;
	}

	protected function deletable(): bool
	{
		return true;
	}
}
