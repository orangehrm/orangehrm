import {parse, format, isDate, compareAsc} from 'date-fns';

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

export {isDate, freshDate, parseDate, formatDate, isAfter, isBefore, isEqual};
