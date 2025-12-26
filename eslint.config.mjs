import js from "@eslint/js";
import globals from "globals";
import pluginReact from "eslint-plugin-react";
import {defineConfig} from "eslint/config";
import reactHooks from 'eslint-plugin-react-hooks';
import eslintConfigPrettier from "eslint-config-prettier/flat";

export default defineConfig([
    {
        files: ["**/*.{js,mjs,cjs,jsx}"],
        plugins: {js},
        extends: ["js/recommended"],
        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node,
            },
        }
    },
    pluginReact.configs.flat.recommended,
    reactHooks.configs.flat.recommended,
    eslintConfigPrettier,
]);
