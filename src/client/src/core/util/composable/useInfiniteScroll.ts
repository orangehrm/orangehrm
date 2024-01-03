/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

import {ref, nextTick, onMounted, onBeforeUnmount} from 'vue';
import {promiseDebounce} from '@ohrm/oxd';

type useInfiniteScrollArgs = {
  refName?: string;
  scrollDistance?: number;
  debounceInterval?: number;
};

export interface CustomElement extends HTMLElement {
  $el: HTMLElement;
}

export default function useInfiniteScroll(
  executor: () => void,
  {
    refName = 'scrollerRef',
    scrollDistance = 100,
    debounceInterval = 100,
  }: useInfiniteScrollArgs = {},
) {
  let scrolledAmount = 0,
    isScrollDown = false;
  const scrollContainer = ref<CustomElement>();
  const onScroll = promiseDebounce(async () => executor(), debounceInterval);

  const onScrollEvent = () => {
    let scrollHeight, clientHeight, scrollTop;

    if (scrollContainer.value) {
      ({scrollHeight, clientHeight, scrollTop} =
        scrollContainer.value.$el || scrollContainer.value);
    } else {
      scrollTop = window.scrollY;
      scrollHeight = document.body.scrollHeight;
      clientHeight = document.body.clientHeight;
    }

    // compare previous scroll with current scroll top to find vertical direction
    isScrollDown = scrollTop > scrolledAmount;
    scrolledAmount = scrollTop;

    // clientHeight = inner height of an element in pixels (without overflow)
    // scrollHeight = inner height of an element in pixels including overflown content
    // scrollTop = how much content is scrolled vertically in pixels
    const scrollerAtBottom =
      scrollTop + clientHeight >= scrollHeight - (scrollDistance || 0);

    if (isScrollDown && scrollerAtBottom) onScroll();
  };

  onMounted(async () => {
    await nextTick();
    if (scrollContainer.value) {
      (scrollContainer.value.$el || scrollContainer.value).addEventListener(
        'scroll',
        onScrollEvent,
      );
    } else {
      document.addEventListener('scroll', onScrollEvent);
    }
  });

  onBeforeUnmount(() => {
    if (scrollContainer.value) {
      (scrollContainer.value.$el || scrollContainer.value).removeEventListener(
        'scroll',
        onScrollEvent,
      );
    } else {
      document.removeEventListener('scroll', onScrollEvent);
    }
  });

  return {
    [refName]: scrollContainer,
  };
}
