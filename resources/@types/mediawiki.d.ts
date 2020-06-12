interface MediaWiki {
  now(): number;

  track(topic: string, data?: Object | number | string): void;

  msg(key: string, ...parameters: any): string;

  util: {
    getUrl(pageName?: string, params?: Object): string;
  };

  loader: {
    using(
      dependencies: string | string[],
      ready?: Function,
      error?: Function
    ): Promise<any>;
  };

  config: {
    get(selection?: string | string[], fallback?: any): any | Object | null;
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
