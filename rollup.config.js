import resolve from 'rollup-plugin-node-resolve';
import commonjs from 'rollup-plugin-commonjs';
import { terser } from 'rollup-plugin-terser';
import babel from 'rollup-plugin-babel';
import cleanup from 'rollup-plugin-cleanup';

const is_watch = process.env.ROLLUP_WATCH;

const plugins = [
  resolve({
    browser: true,
  }),
  commonjs(),
  babel({
    exclude: 'node_modules/**',
    presets: [
      [
        "@babel/preset-env",
        {
          "targets": "last 2 versions",
          "loose": true
        }
      ]
    ]
  }),
  cleanup(),
  !is_watch && terser()
];

// Entry create.
const entry = (input, output) => {
  return {
    input: input,
    output: [
      {
        file: output,
        format: 'iife',
        // Set name from output.file name without extension.
        name: output.replace(/^.*[\\\/]/, '').split('.').slice(0, -1).join('.'),
        globals: {
          Drupal: 'Drupal',
          drupalSettings: 'drupalSettings',
        },
      },
    ],
    external: ['drupalCore', 'drupalSettingsCore'],
    plugins: plugins,
  }
};

// Return entries
export default [
  entry('assets/js/src/index.js', 'assets/js/init.js'),
];
