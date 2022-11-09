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

import {ref} from 'vue';

export default function useProgress() {
  let time = 0;
  let working = true;
  let generator: Generator<number>;
  const frequency = 100;
  const progress = ref(0);

  function* exponentGenerator() {
    const _progress = 0;
    while (_progress < 1) {
      // simplified implementation from https://github.com/piercus/fake-progress
      yield 1 - Math.exp((-1 * time) / 1000);
      time += frequency;
    }
  }

  const increment = () => {
    setTimeout(() => {
      if (progress.value === 100 || !working) return;
      progress.value = generator.next().value * 100;
      increment();
    }, frequency + Math.random() * 500);
  };

  const start = () => {
    time = 0;
    working = true;
    progress.value = 0;
    generator = exponentGenerator();
    increment();
  };

  const end = () => {
    progress.value = 100;
  };

  const stop = () => {
    working = false;
  };

  return {
    end,
    stop,
    start,
    progress,
  };
}
