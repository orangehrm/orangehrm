import {
  parse,
  format,
  isDate,
  compareAsc,
  startOfYear,
  endOfYear,
  getDaysInMonth,
  addDays,
  isSameDay,
  differenceInSeconds,
  differenceInCalendarDays,
} from 'date-fns';

const defaultTimezones = [
  {
    offset: 0,
    label: 'Europe/London',
  },
  {
    offset: 1,
    label: 'Europe/Belgrade',
  },
  {
    offset: 2,
    label: 'Europe/Minsk',
  },
  {
    offset: 3,
    label: 'Asia/Kuwait',
  },
  {
    offset: 4,
    label: 'Asia/Muscat',
  },
  {
    offset: 5,
    label: 'Asia/Yekaterinburg',
  },
  {
    offset: 5.5,
    label: 'Asia/Kolkata',
  },
  {
    offset: 6,
    label: 'Asia/Dhaka',
  },
  {
    offset: 7,
    label: 'Asia/Krasnoyarsk',
  },
  {
    offset: 8,
    label: 'Asia/Brunei',
  },
  {
    offset: 9,
    label: 'Asia/Seoul',
  },
  {
    offset: 9.5,
    label: 'Australia/Darwin',
  },
  {
    offset: 10,
    label: 'Australia/Canberra',
  },
  {
    offset: 11,
    label: 'Asia/Magadan',
  },
  {
    offset: 12,
    label: 'Pacific/Fiji',
  },
  {
    offset: -11,
    label: 'Pacific/Midway',
  },
  {
    offset: -10,
    label: 'Pacific/Honolulu',
  },
  {
    offset: -9,
    label: 'America/Anchorage',
  },
  {
    offset: -8,
    label: 'America/Los_Angeles',
  },
  {
    offset: -7,
    label: 'America/Denver',
  },
  {
    offset: -6,
    label: 'America/Tegucigalpa',
  },
  {
    offset: -5,
    label: 'America/New_York',
  },
  {
    offset: -4,
    label: 'America/Halifax',
  },
  {
    offset: -3.5,
    label: 'America/St_Johns',
  },
  {
    offset: -3,
    label: 'America/Argentina/Buenos_Aires',
  },
  {
    offset: -2,
    label: 'Atlantic/South_Georgia',
  },
  {
    offset: -1,
    label: 'Atlantic/Azores',
  },
];

const freshDate = () => {
  return new Date(new Date().setHours(0, 0, 0, 0));
};

const parseDate = (value: string, dateFormat: string): Date | null => {
  try {
    const parsed = parse(value, dateFormat, freshDate());
    return !isNaN(parsed.valueOf()) ? parsed : null;
  } catch (error) {
    return null;
  }
};

const formatDate = (value: Date, dateFormat: string): string | null => {
  try {
    return format(value, dateFormat);
  } catch (error) {
    return null;
  }
};

const isBefore = (
  reference: string,
  comparable: string,
  dateFormat: string,
): boolean => {
  const referenceDate = parseDate(reference, dateFormat);
  const comparableDate = parseDate(comparable, dateFormat);

  if (referenceDate && comparableDate) {
    return compareAsc(referenceDate, comparableDate) === -1 ? true : false;
  }

  return false;
};

const isAfter = (
  reference: string,
  comparable: string,
  dateFormat: string,
): boolean => {
  const referenceDate = parseDate(reference, dateFormat);
  const comparableDate = parseDate(comparable, dateFormat);

  if (referenceDate && comparableDate) {
    return compareAsc(referenceDate, comparableDate) === 1 ? true : false;
  }

  return false;
};

const isEqual = (
  reference: string,
  comparable: string,
  dateFormat: string,
): boolean => {
  const referenceDate = parseDate(reference, dateFormat);
  const comparableDate = parseDate(comparable, dateFormat);

  if (referenceDate && comparableDate) {
    return compareAsc(referenceDate, comparableDate) === 0 ? true : false;
  }

  return false;
};

const numberOfDaysInMonth = (
  month: number | undefined, // 1 - 12
  discardLeapYear: boolean,
): number => {
  if (month && month > 0 && month <= 12) {
    const days = getDaysInMonth(new Date().setMonth(month - 1));
    return discardLeapYear && days === 29 ? 28 : days;
  }

  return 0;
};

const parseTime = (value: string, timeFormat: string): Date | null => {
  return parseDate(value, timeFormat);
};

const compareTime = (
  reference: string,
  comparable: string,
  timeFormat: string,
): number => {
  const referenceTime = parseDate(reference, timeFormat);
  const comparableTime = parseDate(comparable, timeFormat);

  if (referenceTime && comparableTime) {
    if (referenceTime.valueOf() < comparableTime.valueOf()) {
      return 1;
    }
    if (referenceTime.valueOf() > comparableTime.valueOf()) {
      return -1;
    }
    if (referenceTime.valueOf() === comparableTime.valueOf()) {
      return 0;
    }
  }

  return NaN;
};

const diffInDays = (
  fromDate: string,
  toDate: string,
  dateFormat = 'yyyy-MM-dd',
): number => {
  const from = parseDate(fromDate, dateFormat);
  const to = parseDate(toDate, dateFormat);
  if (from && to) {
    return isSameDay(to, from) ? 1 : differenceInCalendarDays(to, from) + 1;
  }
  return 0;
};

const diffInTime = (
  startTime: string,
  endTime: string,
  timeFormat = 'HH:mm',
): number => {
  const start = parseTime(startTime, timeFormat);
  const end = parseTime(endTime, timeFormat);
  if (start && end) {
    const diffInSecs = differenceInSeconds(end, start);
    if (diffInSecs > 0) return diffInSecs;
  }
  return 0;
};

const secondsTohhmm = (seconds: number): string => {
  const hours = Math.floor(seconds / 3600);
  const minutes = Math.floor((seconds - hours * 3600) / 60);
  return `${hours.toString().padStart(2, '0')}:${minutes
    .toString()
    .padStart(2, '0')}`;
};

const parseTimeInSeconds = (value: string): number => {
  // Check if HH:mm format matches else if decimal format
  if (/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(value)) {
    const time = value.split(':');
    return parseInt(time[0]) * 60 * 60 + parseInt(time[1]) * 60;
  } else if (
    parseFloat(value) < 24 &&
    /^([0-9])+(?:\.[0-9]{1,2})?$/.test(value)
  ) {
    return parseFloat(value) * 60 * 60;
  } else {
    return -1;
  }
};

/**
 * setClockInterval will repeatedly calls a function or executes a code snippet,
 * while being in sync with system clock. minimum resoluton 1 second.
 * @param callback {function():void} callback function to execute
 * @param interval {number} interval in miliseconds. default 1000
 */
const setClockInterval = (callback: (args: void) => void, interval = 1000) => {
  interval = interval < 1000 ? 1000 : interval; // minimum interval 1000 miliseconds
  const timer = () => {
    callback();
    setTimeout(timer, interval - (new Date().getTime() % interval));
  };
  timer();
};

//this function returns the timezone in standard format eg:- +05:30 when input given as float eg:- +5.5
const getStandardTimezone = (timezoneOffset: number) => {
  return (
    (timezoneOffset > 0 ? '+' : '-') +
    String(Math.abs(timezoneOffset).toFixed(2))
      .split('.')
      .map((substr, i) =>
        i === 0
          ? substr.padStart(2, '0')
          : String(parseInt(substr) * 0.6).padEnd(2, '0'),
      )
      .join(':')
  );
};

/**
 * guessTimezone will first try to guess the current timezone name using
 * ES6 Intl API. in the offchance it's not possible it will revert value using
 * default timezone list.
 * @typedef {Object} Timezone
 * @property {string} name - timezone's english name
 * @property {string} label - timezone's english formatted label
 * @property {number} offset - timezone's offset in hours
 */
const guessTimezone = () => {
  let timezoneName = Intl.DateTimeFormat().resolvedOptions().timeZone;
  // getTimezoneOffset return difference in minutes between UTC and client
  // offset is positive if the local timezone is behind UTC and negative if it is ahead
  const timezoneOffset = (new Date().getTimezoneOffset() / 60) * -1;
  if (timezoneName === undefined) {
    // assign timezone manually
    const resolvedTz = defaultTimezones.find(
      tz => tz.offset === timezoneOffset,
    );
    timezoneName = resolvedTz ? resolvedTz.label : defaultTimezones[0].label;
  }

  const formattedOffset = getStandardTimezone(timezoneOffset);

  return {
    name: timezoneName,
    label: `(GMT ${formattedOffset}) ${timezoneName}`,
    offset: timezoneOffset,
  };
};

export {
  isDate,
  freshDate,
  parseDate,
  formatDate,
  isAfter,
  isBefore,
  isEqual,
  startOfYear,
  endOfYear,
  numberOfDaysInMonth,
  addDays,
  parseTime,
  diffInDays,
  diffInTime,
  secondsTohhmm,
  compareTime,
  parseTimeInSeconds,
  setClockInterval,
  guessTimezone,
  getStandardTimezone,
};
