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
        '@orangehrm/util': '@/util',
        '@orangehrm/components': '@/components',
      },
      modules: ['../plugins/orangehrmAdminPlugin/client'],
    },
  },
  publicPath: '.',
  pages: {
    jobTitle: {
      entry: '../plugins/orangehrmAdminPlugin/client/jobTitle/index.js',
      template: 'public/index.html',
      filename: 'admin_saveJobTitle.php',
      title: 'orangehrm',
    },
  },
};
