interface MwWindowOO {
  ui: {
    infuse(idOrNode: string | HTMLElement | JQuery, config?: Object): Element;

    confirm(text: JQuery | string, options?: object): JQuery.Promise<any>;
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

  connect(context: object, methods: object): EventEmitter;
}

interface PopupWidget extends EventEmitter {
  toggle(show?: boolean): Element;
}

declare const OO: MwWindowOO;
