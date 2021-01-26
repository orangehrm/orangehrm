module.exports = {
  css: {
    loaderOptions: {
      sass: {
        prependData: `
              @import "@/styles/_variables.scss";
            `,
      },
    },
  },
  configureWebpack: {
    resolve: {
      alias: {
        '@orangehrm/oxd': '@orangehrm/oxd/src',
      },
    },
  },
  publicPath: '.',
};
