// import { Nullable, SelectOptionType } from '@/types/types';

export const dateFormat = 'yyyy-MM-dd';
export const dateTimeFormat = 'yyyy-MM-dd HH:mm:ss';

type PreventDefault = Pick<React.FormEvent, 'preventDefault'>['preventDefault'];
type StopPropagation = Pick<
  React.FormEvent,
  'stopPropagation'
>['stopPropagation'];

export function preventNativeSubmit<
  T extends {
    preventDefault: PreventDefault;
    stopPropagation: StopPropagation;
  }
>(callback: any) {
  return function (e: T) {
    e.preventDefault();
    e.stopPropagation();
    callback(e);
  };
}

export function formatAsCurrency(num: number) {
  const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'NGN'
  });
  return formatter.format(num);
}

export function setUrlFilterOptions(
  option: string,
  filters: {
    [key: string]:
      | { label: string; value: string }
      | null
      | undefined
      | number
      | boolean
      | string;
  },
  url: URL
) {
  const optionValue = filters[option];

  if (!optionValue) {
    url.searchParams.delete(option);
    url.searchParams.delete(`${option}Label`);
    return;
  }

  if (
    typeof optionValue === 'string' ||
    typeof optionValue === 'number' ||
    typeof optionValue === 'boolean'
  ) {
    url.searchParams.set(option, optionValue + '');
    return;
  }

  url.searchParams.set(option, optionValue?.value ?? '');
  url.searchParams.set(`${option}Label`, optionValue?.label ?? '');
}

export function generateRandomString(length = 10): string {
  const characters =
    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';

  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    result += characters.charAt(randomIndex);
  }

  return result;
}

export function blurStr(str: string) {
  const convertedString = str.replace(/^(..).*(..)$/, '$1********$2');
  return convertedString;
}

export function formatTime(timeInSecs: number) {
  if (isNaN(timeInSecs) || timeInSecs < 0) {
    timeInSecs = 0;
  }

  const totalMins = timeInSecs / 60;
  let hour: number | string = parseInt(totalMins / 60 + '');
  let min: number | string = parseInt((totalMins % 60) + '');
  let sec: number | string = parseInt((timeInSecs % 60) + '');
  sec = sec < 10 ? '0' + sec : sec;

  if (hour < 1 && min < 1) {
    return sec;
  }

  min = min < 10 ? '0' + min : min;

  if (hour < 1) {
    return min + ':' + sec;
  }

  hour = hour < 10 ? '0' + hour : hour;

  return hour + ':' + min + ':' + sec;
}

export function stripInitials(studentCode: string) {
  const pos = studentCode.indexOf('/');
  return studentCode.substring(pos < 0 ? 0 : pos);
}

export function validFilename(input?: string): string {
  // Remove invalid characters and replace them with underscores
  const sanitizedString = input?.replace(/[^a-zA-Z0-9.-]/g, '_');
  // Ensure the string is not empty after sanitization
  if (!sanitizedString?.trim()) {
    return '';
  }
  return sanitizedString;
}

export function isTimeExpired(timeString?: string): boolean {
  if (!timeString) {
    return false;
  }
  const givenTime = new Date(timeString);
  const currentTime = new Date();
  return givenTime < currentTime;
}
