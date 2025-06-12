import 'jquery';

interface GoupOptions {
  location?: 'left' | 'right';
  locationOffset?: number;
  bottomOffset?: number;
  containerSize?: number;
  containerRadius?: number;
  containerClass?: string;
  arrowClass?: string;
  containerColor?: string;
  arrowColor?: string;
  trigger?: number;
  entryAnimation?: 'fade' | 'slide';
  alwaysVisible?: boolean;
  goupSpeed?: number;
  hideUnderWidth?: number;
  title?: string;
  titleAsText?: boolean;
  titleAsTextClass?: string;
  zIndex?: number;
  [key: string]: any;
}
interface JQueryStatic {
  goup(options?: GoupOptions): void;
}