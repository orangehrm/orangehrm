/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

(function () {
    'use strict';

    angular
        .module('app.help')
        .factory('labelHelper', labelHelper);

    labelHelper.$inject = ['$state', '$window'];

    /* @ngInject */
    function labelHelper($state, $window) {
        return {
            generateLabel: generateLabel,
            downloadAllStates: downloadAllStates

        };

        function generateLabel() {
            var label = '';
            if($state.router.globals.current.name === "noncore" || $state.router.globals.current.name === "noncore.WithParams") {
                label = $state.router.globals.params.module + "_" + $state.router.globals.params.action;
            } else {
                label =  $state.router.globals.current.name;
                label = label.replace(/\./g, "_")
            }
            return label;
        }

        function downloadAllStates() {
            var finalString = [];
            var maxStringLength = 2;
            var statesToSkip = [
                "403",
                "404",
                "",
                "pim",
                "maintenance",
                "admin",
                "leave",
                "time",
                "noncore",
                "noncore.WithParams"
            ];
            angular.forEach($state.get(), function(value, key){
                if(statesToSkip.indexOf(value.name) === -1) {
                    var state = value.name;
                    var label = value.name.replace(/\./g, "_");
                    var url = value.url;
                    var row = {
                        'State_Name': state,
                        'Label': label,
                        'Url_Pattern': url
                    };
                    finalString.push(row);
                }

            });
            saveTextAsFile((JSON.stringify(finalString, null, 2)), "angular_labels.json");
            // angular.element($('#wrapper').find('div').find('div')).controller().labelHelper.downloadAllStates()
            // angular.element($('#wrapper').find('div').find('div')).controller().labelHelper.getLabelForScreen()
        }

        function saveTextAsFile (data, filename){

            if(!data) {
                console.error('Console.save: No data')
                return;
            }

            if(!filename) filename = 'console.json'

            var blob = new Blob([data], {type: 'text/plain'}),
                e    = document.createEvent('MouseEvents'),
                a    = document.createElement('a')
// FOR IE:
            if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveOrOpenBlob(blob, filename);
            }
            else{
                var e = document.createEvent('MouseEvents'),
                    a = document.createElement('a');

                a.download = filename;
                a.href = window.URL.createObjectURL(blob);
                a.dataset.downloadurl = ['text/plain', a.download, a.href].join(':');
                e.initEvent('click', true, false, window,
                    0, 0, 0, 0, 0, false, false, false, false, 0, null);
                a.dispatchEvent(e);
            }
        }
    }
})();
//devTool
