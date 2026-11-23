let colors = {
    "th-primary-color": "#06347d",
    "th-secondary-color": "#0ac989"
};

module.exports = {
    plugins: [
        require('postcss-import'),
        require('tailwindcss/nesting'),
        require('tailwindcss'),
        require('autoprefixer'),
        require('postcss-simple-vars')({ variables: colors })
    ]
}
