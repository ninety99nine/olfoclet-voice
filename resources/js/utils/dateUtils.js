import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

// Extend Day.js with the relativeTime plugin for "time ago" formatting
dayjs.extend(relativeTime);

/**
 * Formats a given date as "14 Jul 2024".
 *
 * @param {string|Date} date - The date to format.
 * @returns {string} - Formatted date in "DD MMM YYYY" format.
 *
 * @example
 * formattedDate('2024-07-14'); => "14 Jul 2024"
 */
export function formattedDate(date) {
    return dayjs(date).format('DD MMM YYYY');
}

/**
 * Formats a given date as "14 Jul 2024 15:30".
 *
 * @param {string|Date} date - The date to format.
 * @returns {string} - Formatted date and time in "DD MMM YYYY HH:mm" format.
 *
 * @example
 * formattedDatetime('2024-07-14T15:30:00'); => "14 Jul 2024 15:30"
 */
export function formattedDatetime(date) {
    return dayjs(date).format('DD MMM YYYY HH:mm');
}

/**
 * Formats a given date as "Jul 14, 2024" (Month Day, Year).
 *
 * @param {string|Date} date - The date to format.
 * @returns {string} - Formatted long date in "MMM DD, YYYY" format.
 *
 * @example
 * formattedLongDate('2024-07-14'); => "Jul 14, 2024"
 */
export function formattedLongDate(date) {
    return dayjs(date).format('MMM DD, YYYY');
}

/**
 * Formats a given date to return the short weekday name (e.g., "Mon", "Tue").
 *
 * @param {string|Date} date - The date to format.
 * @returns {string} - The short weekday name in "ddd" format.
 *
 * @example
 * formattedWeekday('2024-07-14'); => "Sun"
 * formattedWeekday('2024-07-15'); => "Mon"
 */
export function formattedShortWeekday(date) {
    return dayjs(date).format('ddd');
}

/**
 * Returns a relative time string (e.g., "10 minutes ago" or "in 3 days").
 *
 * @param {string|Date} date - The date to convert.
 * @param {boolean} withoutSuffix - If true, removes "ago" or "in" (default: false).
 * @returns {string} - A relative time string.
 *
 * @example
 * formattedRelativeDate('2024-07-14T15:30:00'); => "3 hours ago"
 * formattedRelativeDate('2024-07-16', true); => "2 days"
 */
export function formattedRelativeDate(date, withoutSuffix = false) {
    return dayjs(date).fromNow(withoutSuffix);
}

/**
 * Formats a given datetime as "03:45 PM" (12-hour time format with AM/PM).
 *
 * @param {string|Date} datetime - The datetime to format.
 * @returns {string} - Formatted time in "hh:mm A" format.
 *
 * @example
 * formattedTime('2024-07-14T15:45:00'); => "03:45 PM"
 */
export function formattedTime(datetime) {
    return dayjs(datetime).format('hh:mm A');
}

/**
 * Converts a given date to a Unix timestamp.
 *
 * @param {string|Date} date - The date to convert.
 * @returns {number} - Unix timestamp (seconds since 1970).
 *
 * @example
 * dateToTimestamp('2024-07-14'); => 1720915200
 */
export function dateToTimestamp(date) {
    return dayjs(date).unix();
}

/**
 * Checks if a given date is in the future.
 *
 * @param {string|Date} date - The date to check.
 * @returns {boolean} - True if the date is in the future, otherwise false.
 *
 * @example
 * isFutureDate('2025-01-01'); => true
 * isFutureDate('2023-01-01'); => false
 */
export function isFutureDate(date) {
    return dayjs(date).isAfter(dayjs());
}

/**
 * Checks if a given date is in the past.
 *
 * @param {string|Date} date - The date to check.
 * @returns {boolean} - True if the date is in the past, otherwise false.
 *
 * @example
 * isPastDate('2023-01-01'); => true
 * isPastDate('2025-01-01'); => false
 */
export function isPastDate(date) {
    return dayjs(date).isBefore(dayjs());
}
