// mw.echo.api.EchoApi
interface EchoApi {
  fetchNotifications(
    type: string,
    sources?: string | string[],
    isForced?: boolean,
    filters?: Object
  ): Promise<any>;
}

// mw.echo.dm.ModelManager
interface ModelManager extends EventEmitter {}

// mw.echo.dm.UnreadNotificationCounter
interface UnreadNotificationCounter {}

// mw.echo.ui.NotificationBadgeWidget
interface NotificationBadgeWidget extends EventEmitter {
  $element: JQuery<HTMLElement>;

  popup: PopupWidget;
}

// mw.echo.Controller
interface Controller {}
