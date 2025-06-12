/**
 * HTML Legend plugin for Chart.js
 * Renders the legend items as HTML elements with custom styling
 */

// Type for Chart.js chart instance
interface Chart {
  config: {
    type: string;
  };
  options: {
    plugins: {
      legend: {
        labels: {
          generateLabels: (chart: Chart) => LegendItem[];
        };
      };
    };
  };
  toggleDataVisibility: (index: number) => void;
  setDatasetVisibility: (datasetIndex: number, visibility: boolean) => void;
  isDatasetVisible: (datasetIndex: number) => boolean;
  update: () => void;
}

// Type for legend item
interface LegendItem {
  text: string;
  fillStyle: string;
  strokeStyle: string;
  lineWidth: number;
  hidden: boolean;
  index: number;
  datasetIndex: number;
  fontColor: string;
}

// Type for plugin options
interface HtmlLegendOptions {
  containerID: string;
}

/**
 * Gets or creates a legend list container
 * @param chart - The chart instance
 * @param id - The container ID
 * @returns The list container element
 */
const getOrCreateLegendList = (chart: Chart, id: string): HTMLUListElement => {
  const legendContainer = document.getElementById(id);
  
  if (!legendContainer) {
    throw new Error(`Legend container with id '${id}' not found`);
  }
  
  let listContainer = legendContainer.querySelector('ul');

  if (!listContainer) {
    listContainer = document.createElement('ul');
    listContainer.style.display = 'flex';
    listContainer.style.flexDirection = 'row';
    listContainer.style.margin = '0';
    listContainer.style.padding = '0';

    legendContainer.appendChild(listContainer);
  }

  return listContainer as HTMLUListElement;
};

/**
 * HTML Legend Plugin for Chart.js
 */
export const htmlLegendPlugin = {
  id: 'htmlLegend',
  afterUpdate(chart: Chart, args: unknown, options: HtmlLegendOptions): void {
    const ul = getOrCreateLegendList(chart, options.containerID);

    // Remove old legend items
    while (ul.firstChild) {
      ul.firstChild.remove();
    }

    // Reuse the built-in legendItems generator
    const items = chart.options.plugins.legend.labels.generateLabels(chart);

    items.forEach(item => {
      const li = document.createElement('li');
      li.style.alignItems = 'center';
      li.style.cursor = 'pointer';
      li.style.display = 'flex';
      li.style.flexDirection = 'row';
      li.style.marginLeft = '10px';

      li.onclick = () => {
        const {type} = chart.config;
        if (type === 'pie' || type === 'doughnut') {
          // Pie and doughnut charts only have a single dataset and visibility is per item
          chart.toggleDataVisibility(item.index);
        } else {
          chart.setDatasetVisibility(item.datasetIndex, !chart.isDatasetVisible(item.datasetIndex));
        }
        chart.update();
      };

      // Color box
      const boxSpan = document.createElement('span');
      boxSpan.style.background = item.fillStyle;
      boxSpan.style.borderColor = item.strokeStyle;
      boxSpan.style.borderWidth = item.lineWidth + 'px';
      boxSpan.style.display = 'inline-block';
      boxSpan.style.flexShrink = '0';
      boxSpan.style.height = '20px';
      boxSpan.style.marginRight = '10px';
      boxSpan.style.width = '20px';

      // Text
      const textContainer = document.createElement('p');
      textContainer.style.color = item.fontColor;
      textContainer.style.margin = '0';
      textContainer.style.padding = '0';
      textContainer.style.textDecoration = item.hidden ? 'line-through' : '';

      const text = document.createTextNode(item.text);
      textContainer.appendChild(text);

      li.appendChild(boxSpan);
      li.appendChild(textContainer);
      ul.appendChild(li);
    });
  }
};