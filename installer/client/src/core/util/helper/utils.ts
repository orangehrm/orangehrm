export function throttle(fn: () => void, wait: number) {
  let throttled = false;
  return function (...args: unknown[]) {
    if (!throttled) {
      fn.apply(this, args);
      throttled = true;
      setTimeout(() => {
        throttled = false;
      }, wait);
    }
  };
}

export function debounce(fn: () => void, wait: number) {
  let timer: number | null;
  return function (...args: unknown[]) {
    if (timer) {
      clearTimeout(timer); // clear any pre-existing timer
    }
    // eslint-disable-next-line @typescript-eslint/no-this-alias
    const context = this; // get the current context
    timer = setTimeout(() => {
      fn.apply(context, args); // call the function if time expires
    }, wait);
  };
}
