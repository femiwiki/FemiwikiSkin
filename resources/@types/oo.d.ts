interface MwWindowOO {
  ui: {
    infuse(idOrNode: string | HTMLElement | JQuery, config?: Object): Element;
  };
}

interface Element {
  on(events: string, handler: Function): () => JQuery;
}

interface EventEmitter {
  once(event: string, listener: Function): EventEmitter;

  on(
    event: string,
    method: Function | string,
    args?: Array<any>,
    context?: Object
  ): EventEmitter;
}

interface PopupWidget extends EventEmitter {
  toggle(show?: boolean): Element;
}

declare const OO: MwWindowOO;
