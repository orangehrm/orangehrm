module.exports = {
  css: {
    loaderOptions: {
      sass: {
        prependData: `@import "@/core/styles/_variables.scss";`,
      },
    },
    extract: true,
  },
  configureWebpack: {
    resolve: {
      alias: {
        '@orangehrm/oxd': '@orangehrm/oxd/src',
        '@orangehrm/util': '@/core/util',
        '@orangehrm/components': '@/core/components',
        assets: '@orangehrm/oxd/src/assets',
      },
    },
  },
  chainWebpack: config => {
    config.plugins.delete('html');
    config.plugins.delete('preload');
    config.plugins.delete('prefetch');
  },
  publicPath: '.',
  filenameHashing: false,
  runtimeCompiler: true,
};
