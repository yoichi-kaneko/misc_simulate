import { defineConfig } from "eslint/config";
import globals from "globals";
import path from "node:path";
import { fileURLToPath } from "node:url";
import js from "@eslint/js";
import { FlatCompat } from "@eslint/eslintrc";
import tseslint from "@typescript-eslint/eslint-plugin";
import tseslintParser from "@typescript-eslint/parser";
import reactPlugin from "eslint-plugin-react";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const compat = new FlatCompat({
    baseDirectory: __dirname,
    recommendedConfig: js.configs.recommended,
    allConfig: js.configs.all
});

export default defineConfig([{
    extends: compat.extends(
        "eslint:recommended",
        "plugin:@typescript-eslint/recommended",
        "plugin:react/recommended",
    ),
    languageOptions: {
        globals: {
            ...globals.browser,
        },
        ecmaVersion: 2022,
        sourceType: "module",
        parser: tseslintParser,
        parserOptions: {
            ecmaFeatures: { jsx: true },
        },
    },
    plugins: {
        "@typescript-eslint": tseslint,
        react: reactPlugin,
    },
    rules: {
        indent: ["error", 2],
    },
}]);
