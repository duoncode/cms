-- Basic content types for integration testing
-- These types can be used across multiple test cases

-- Test page types
INSERT INTO cms.types (handle, kind) VALUES
    ('test-page', 'page'),
    ('test-home', 'page'),
    ('test-article', 'page')
ON CONFLICT (handle) DO NOTHING;

-- Test document types
INSERT INTO cms.types (handle, kind) VALUES
    ('test-document', 'document'),
    ('test-media', 'document')
ON CONFLICT (handle) DO NOTHING;

-- Test block types
INSERT INTO cms.types (handle, kind) VALUES
    ('test-block', 'block'),
    ('test-widget', 'block')
ON CONFLICT (handle) DO NOTHING;
