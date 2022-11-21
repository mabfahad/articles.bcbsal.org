module.exports = {
  extends: 'stylelint-config-standard-scss',
  plugins: ['stylelint-order'],
  rules: {
    'block-no-empty': true,
    'color-no-invalid-hex': true,
    'comment-no-empty': true,
    'declaration-block-no-redundant-longhand-properties': [
      true,
      {
        ignoreShorthands: ['/flex/']
      }
    ],
    'function-linear-gradient-no-nonstandard-direction': true,
    'keyframe-declaration-no-important': true,
    'media-feature-name-no-unknown': true,
    'no-duplicate-at-import-rules': true,
    'no-empty-source': true,
    'no-extra-semicolons': true,
    'no-invalid-double-slash-comments': true,
    'selector-pseudo-class-no-unknown': true,
    'selector-pseudo-element-no-unknown': true,
    'string-no-newline': true,
    'unit-no-unknown': true,
    'indentation': 4,
    'max-empty-lines': 2,
    'rule-empty-line-before': [
      'always',
      {
        except: ['inside-block'],
        ignore: ['after-comment'],
      },
    ],
    'order/properties-alphabetical-order': true,
    'declaration-empty-line-before': 'never',
    'selector-list-comma-newline-after': 'always-multi-line',
    'max-nesting-depth': 6,
    'declaration-block-no-shorthand-property-overrides': true,
    'no-descending-specificity': null,
    'no-duplicate-selectors': true,
    'function-name-case': 'lower',
    'number-max-precision': 10,
    'selector-class-pattern': '^\.[a-z]([a-z0-9-]+)?(__([a-z0-9]+-?)+)?(--([a-z0-9]+-?)+){0,2}$'
  },
}
