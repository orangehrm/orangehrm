<!--
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
 -->

<template>
  <div
    :class="{
      'orangehrm-like-animation': like,
    }"
  >
    <svg
      id="heart-svg"
      class="orangehrm-heart-icon"
      viewBox="467 392 58 57"
      xmlns="http://www.w3.org/2000/svg"
      @click="addLike"
    >
      <g
        id="Group"
        fill="none"
        fill-rule="evenodd"
        transform="translate(467 392)"
      >
        <path
          id="heart"
          class="orangehrm-heart-icon-path"
          d="M29.144 20.773c-.063-.13-4.227-8.67-11.44-2.59C7.63 28.795 28.94 43.256 29.143 43.394c.204-.138 21.513-14.6 11.44-25.213-7.214-6.08-11.377 2.46-11.44 2.59z"
          fill="#64728c"
        />

        <g
          class="orangehrm-heart-icon-group7"
          opacity="0"
          transform="translate(7 6)"
        >
          <circle
            class="orangehrm-heart-icon-oval1"
            fill="#9CD8C3"
            cx="2"
            cy="6"
            r="2"
          />
          <circle
            class="orangehrm-heart-icon-oval2"
            fill="#8CE8C3"
            cx="5"
            cy="2"
            r="2"
          />
        </g>

        <g
          class="orangehrm-heart-icon-group6"
          opacity="0"
          transform="translate(0 28)"
        >
          <circle
            class="orangehrm-heart-icon-oval1"
            fill="#CC8EF5"
            cx="2"
            cy="7"
            r="2"
          />
          <circle
            class="orangehrm-heart-icon-oval2"
            fill="#91D2FA"
            cx="3"
            cy="2"
            r="2"
          />
        </g>

        <g
          class="orangehrm-heart-icon-group3"
          opacity="0"
          transform="translate(52 28)"
        >
          <circle
            class="orangehrm-heart-icon-oval2"
            fill="#9CD8C3"
            cx="2"
            cy="7"
            r="2"
          />
          <circle
            class="orangehrm-heart-icon-oval1"
            fill="#8CE8C3"
            cx="4"
            cy="2"
            r="2"
          />
        </g>

        <g
          class="orangehrm-heart-icon-group2"
          opacity="0"
          transform="translate(44 6)"
        >
          <circle
            class="orangehrm-heart-icon-oval2"
            fill="#CC8EF5"
            cx="5"
            cy="6"
            r="2"
          />
          <circle
            class="orangehrm-heart-icon-oval1"
            fill="#CC8EF5"
            cx="2"
            cy="2"
            r="2"
          />
        </g>

        <g
          class="orangehrm-heart-icon-group5"
          opacity="0"
          transform="translate(14 50)"
        >
          <circle
            class="orangehrm-heart-icon-oval1"
            fill="#91D2FA"
            cx="6"
            cy="5"
            r="2"
          />
          <circle
            class="orangehrm-heart-icon-oval2"
            fill="#91D2FA"
            cx="2"
            cy="2"
            r="2"
          />
        </g>

        <g
          class="orangehrm-heart-icon-group4"
          opacity="0"
          transform="translate(35 50)"
        >
          <circle
            class="orangehrm-heart-icon-oval1"
            fill="#F48EA7"
            cx="6"
            cy="5"
            r="2"
          />
          <circle
            class="orangehrm-heart-icon-oval2"
            fill="#F48EA7"
            cx="2"
            cy="2"
            r="2"
          />
        </g>

        <g
          class="orangehrm-heart-icon-group1"
          opacity="0"
          transform="translate(24)"
        >
          <circle
            class="orangehrm-heart-icon-oval1"
            fill="#9FC7FA"
            cx="2.5"
            cy="3"
            r="2"
          />
          <circle
            class="orangehrm-heart-icon-oval2"
            fill="#9FC7FA"
            cx="7.5"
            cy="2"
            r="2"
          />
        </g>
      </g>
    </svg>
    <span class="orangehrm-heart-icon-circle"></span>
  </div>
</template>

<script>
export default {
  name: 'PostLikeButton',

  props: {
    like: {
      type: Boolean,
      required: true,
    },
  },
};
</script>

<style lang="scss" src="./post-like-button.scss" scoped></style>
