// mw.echo.api.EchoApi
interface EchoApi {
  fetchNotifications(
    type: string,
    sources?: string | string[],
    isForced?: boolean,
    filters?: Object
  ): Promise<any>;
  markAllRead(source: string, type: string | string[]): Promise<any>;
}

// mw.echo.dm.ModelManager
interface ModelManager extends EventEmitter {
  getFiltersModel(): FiltersModel;
}

interface FiltersModel extends EventEmitter {
  getSourcePagesModel(): SourcePagesModel;
}

interface SourcePagesModel extends EventEmitter {
  getCurrentSource(): string;
}

// mw.echo.dm.UnreadNotificationCounter
interface UnreadNotificationCounter {}

// mw.echo.ui.NotificationBadgeWidget
interface NotificationBadgeWidget extends EventEmitter {
  $element: JQuery<HTMLElement>;

  popup: PopupWidget;

  markAllReadButton: ButtonWidget;
}

interface ButtonWidget extends EventEmitter {}

// mw.echo.Controller
interface Controller {}
