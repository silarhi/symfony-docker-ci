const js = require('@eslint/js');
const react = require('eslint-plugin-react');
const reactHooks = require('eslint-plugin-react-hooks');
const prettier = require('eslint-config-prettier');
const babelParser = require('@babel/eslint-parser');

module.exports = [
    js.configs.recommended,
    {
        files: ['assets/**/*.{js,jsx}'],
        languageOptions: {
            parser: babelParser,
            parserOptions: {
                ecmaVersion: 'latest',
                sourceType: 'module',
                ecmaFeatures: {
                    jsx: true,
                },
                requireConfigFile: false,
                babelOptions: {
                    presets: ['@babel/preset-react'],
                },
            },
            globals: {
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
                require: 'readonly',
                module: 'readonly',
                process: 'readonly',
                fetch: 'readonly',
            },
        },
        plugins: {
            react,
            'react-hooks': reactHooks,
        },
        rules: {
            ...react.configs.recommended.rules,
            ...reactHooks.configs.recommended.rules,
            'react/react-in-jsx-scope': 'off',
            'react/prop-types': 'warn',
        },
        settings: {
            react: {
                version: 'detect',
            },
        },
    },
    prettier,
];
