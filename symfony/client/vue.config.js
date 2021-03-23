const pages = require('./pages.json')

module.exports = {
  css: {
    loaderOptions: {
      sass: {
        prependData: `@import "@/styles/_variables.scss";`,
      },
    },
  },
  configureWebpack: {
    resolve: {
      alias: {
        '@orangehrm/oxd': '@orangehrm/oxd/src',
        '@orangehrm/util': '@/util',
        '@orangehrm/components': '@/components',
      },
    },
  },
  publicPath: '.',
  pages,
};
