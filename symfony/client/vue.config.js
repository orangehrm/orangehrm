// eslint-disable-next-line @typescript-eslint/no-var-requires
const DumpBuildTimestampPlugin = require('./scripts/plugins/DumpBuildTimestampPlugin');

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
        '@orangehrm/core': '@/core',
        '@orangehrm/orangehrmAdminPlugin': '@/orangehrmAdminPlugin',
        '@orangehrm/orangehrmAuthenticationPlugin': '@/orangehrmAuthenticationPlugin',
        '@orangehrm/orangehrmPimPlugin': '@/orangehrmPimPlugin',
        assets: '@orangehrm/oxd/src/assets',
      },
    },
    plugins: [new DumpBuildTimestampPlugin()],
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
