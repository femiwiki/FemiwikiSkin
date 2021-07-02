interface MwApi {
  saveOption(name: string, value: unknown): JQuery.Promise<any>;

  get(parameters: object, ajaxOptions?: object): JQuery.Promise<any>;
}

type MwApiConstructor = new (options?: Object) => MwApi;

interface MwTitle {
  getNamespacePrefix(): string;
  getMain(): string;
  getTalkPage(): MwTitle | null;
  getPrefixedText(): string;
}

type MwTitleConstructor = new (title: string, namespace?: string) => MwTitle;

interface MediaWiki {
  now(): number;

  track(topic: string, data?: Object | number | string): void;

  msg(key: string, ...parameters: any): string;

  notify(
    message:
      | HTMLElement
      | HTMLElement[]
      | JQuery
      | MediaWiki['Message']
      | string,
    options?: Object
  ): JQuery.Promise<any>;

  util: {
    getUrl(pageName?: string, params?: Object): string;
  };

  loader: {
    using(
      dependencies: string | string[],
      ready?: Function,
      error?: Function
    ): JQuery.Promise<any>;
  };

  config: {
    get(selection?: string | string[], fallback?: any): any | Object | null;
  };

  user: {
    options: {
      get(selection?: string | string[], fallback?: any): any | Object | null;
    };
  };

  Message: {};

  Api: MwApiConstructor;

  Title: MwTitleConstructor;

  Uri: {
    new (uri?: Object | string, options?: Object | boolean): MediaWiki['Uri'];

    extend(parameters: object): object;
  };

  echo: {
    api: {
      EchoApi: {
        new (): EchoApi;
      };
    };

    ui: {
      NotificationBadgeWidget: {
        new (
          controller: Controller,
          manager: ModelManager,
          links: Object,
          config: Object
        ): NotificationBadgeWidget;
      };

      $overlay: JQuery<HTMLElement>;

      alertWidget: NotificationBadgeWidget;
      messageWidget: NotificationBadgeWidget;
      widget: NotificationBadgeWidget;
    };

    dm: {
      UnreadNotificationCounter: {
        new (
          api: EchoApi,
          type: string,
          max: number,
          config?: Object
        ): UnreadNotificationCounter;
      };
      ModelManager: {
        new (counter: UnreadNotificationCounter, config?: Object): ModelManager;
      };
    };

    Controller: {
      new (echoApi: EchoApi, manager: ModelManager): Controller;
    };
  };

  fw: {};
}

declare const mw: MediaWiki;
