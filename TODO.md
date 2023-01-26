TODO
====

- Improve Html::balanceTags. This is a very naive implementation and does not
  handle singlular tags like <br> or <hr class="whatever">
- Improve Html::excerpt. Check if we're in the middle of an opening tag at the end.
- Add fulltext to builtin page query fields.
- Check JSON values with json schema
- Move docs from mkdocs to vitepress?
- Check entropy algorithm. See: https://github.com/mvhenten/string-entropy/blob/master/index.js
  The example works in another way. We count character classes, the example adds alphabet lengths.
