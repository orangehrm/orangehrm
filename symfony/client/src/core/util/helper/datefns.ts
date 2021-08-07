import {
  parse,
  format,
  isDate,
  compareAsc,
  startOfYear,
  endOfYear,
  getDaysInMonth,
  addDays
} from 'date-fns';

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
  addDays
};
