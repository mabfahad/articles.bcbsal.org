module.exports = {
	root: true,
	env: {
		browser: true,
		jquery: true,
		es6: true,
	},
	parser: '@babel/eslint-parser',
	parserOptions: {
		ecmaVersion: 2020,
		sourceType: 'module',
		babelOptions: {
			configFile: './babel.config.js',
		},
	},
	rules: {
		'no-unused-vars': 1,
		'default-param-last': 1,
		// Do not lint arrow function brace style. https://eslint.org/docs/rules/arrow-body-style
		'arrow-body-style': 'off',
		'babel/valid-typeof': 1,
		// No spaces before the function parens. https://eslint.org/docs/rules/space-before-function-paren
		'space-before-function-paren': [
			'error',
			{
				anonymous: 'never',
				named: 'never',
				asyncArrow: 'always',
			},
		],
		// Require trailing commas on multiline, except functions. https://eslint.org/docs/rules/comma-dangle
		'comma-dangle': [
			'error',
			{
				arrays: 'always-multiline',
				objects: 'always-multiline',
				imports: 'always-multiline',
				exports: 'always-multiline',
				functions: 'always-multiline',
			},
		],
		// Indents are 2 spaces, and indent switch cases one indent level.
		indent: [
			'error',
			2,
			{
				SwitchCase: 1,
			},
		],
		// Don't enforce func-names globally.
		'func-names': 'off',
		'import/no-extraneous-dependencies': [
			'error',
			{ devDependencies: ['**/webpack.*.js', '.scripts/**/*.js', '**/tests/**/*.*(js)'] },
		],
	},
	extends: ['airbnb'],
	plugins: ['babel'],
}
