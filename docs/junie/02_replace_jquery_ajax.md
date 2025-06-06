# jQuery AJAX の代替実装

このファイルには jQuery による ajax 通信の実装があり、これをネイティブな実装に置き換える方法をいくつか提案します。

## 1. Fetch API を使用した実装

最も基本的なネイティブ JavaScript の実装は Fetch API を使用する方法です。

```typescript
// jQuery AJAX の実装
$.ajax({
    type: 'POST',
    url: '/api/nash/calculate',
    data: data,
    success: function (data) {
        // 成功時の処理
    },
    error: function (data) {
        // エラー時の処理
    }
});

// Fetch API を使用した実装
fetch('/api/nash/calculate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify(data)
})
.then(response => {
    if (!response.ok) {
        return response.json().then(errorData => {
            throw errorData;
        });
    }
    return response.json();
})
.then(data => {
    // 成功時の処理
    document.querySelector('button.calculate')?.classList.remove('disabled');
    renderNashSimulationChart(data.render_params);
    renderNashReportArea(data.report_params);
    document.getElementById('nash_spinner')?.style.display = 'none';
    notifyComplete();
})
.catch(error => {
    // エラー時の処理
    setErrorMessage(error);
    const alertElement = document.getElementById('alert_danger');
    const titleElement = document.querySelector('.kt-pagetitle');
    if (alertElement && titleElement) {
        const alertOffset = alertElement.getBoundingClientRect().top;
        const titleOffset = titleElement.getBoundingClientRect().top;
        const offset = alertOffset - titleOffset - 20;
        window.scrollTo({
            top: offset,
            behavior: 'smooth'
        });
    }
    afterCalculateByError('nash_spinner');
});
```

## 2. Axios を使用した実装

bootstrap.js ですでに axios がインポートされているため、これを活用する方法もあります。

```typescript
// axios を使用した実装
axios.post('/api/nash/calculate', data)
    .then(response => {
        // 成功時の処理
        document.querySelector('button.calculate')?.classList.remove('disabled');
        renderNashSimulationChart(response.data.render_params);
        renderNashReportArea(response.data.report_params);
        document.getElementById('nash_spinner')?.style.display = 'none';
        notifyComplete();
    })
    .catch(error => {
        // エラー時の処理
        setErrorMessage(error.response.data);
        const alertElement = document.getElementById('alert_danger');
        const titleElement = document.querySelector('.kt-pagetitle');
        if (alertElement && titleElement) {
            const alertOffset = alertElement.getBoundingClientRect().top;
            const titleOffset = titleElement.getBoundingClientRect().top;
            const offset = alertOffset - titleOffset - 20;
            window.scrollTo({
                top: offset,
                behavior: 'smooth'
            });
        }
        afterCalculateByError('nash_spinner');
    });
```

## 3. React を使用した実装

React を導入する場合は、以下のようなアプローチが考えられます。

### 3.1 React コンポーネントの例

```tsx
import React, { useState } from 'react';
import axios from 'axios';
import { Chart } from 'chart.js';
import katex from 'katex';

interface NashData {
    alpha_1: {
        numerator: string;
        denominator: string;
    };
    // 他のプロパティも同様に定義
}

interface RenderParam {
    title: string;
    display_text: string;
    x: number;
    y: number;
}

interface ReportParam {
    a_rho: string;
}

const NashCalculator: React.FC = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [result, setResult] = useState<{
        render_params: RenderParam[];
        report_params: ReportParam;
    } | null>(null);

    // フォームの値を取得する関数
    const getFormData = (): NashData => {
        return {
            alpha_1: {
                numerator: (document.getElementById('alpha_1_numerator') as HTMLInputElement)?.value || '',
                denominator: (document.getElementById('alpha_1_denominator') as HTMLInputElement)?.value || '',
            },
            // 他のフォーム値も同様に取得
        } as NashData;
    };

    // 計算を実行する関数
    const calculateNash = () => {
        setIsLoading(true);
        setError(null);
        
        const data = getFormData();
        
        axios.post('/api/nash/calculate', data)
            .then(response => {
                setResult(response.data);
                // チャートとレポートの描画は useEffect で行う
            })
            .catch(error => {
                setError(error.response?.data || 'エラーが発生しました');
                // エラー表示のスクロール処理
            })
            .finally(() => {
                setIsLoading(false);
            });
    };

    // チャートを描画する useEffect
    React.useEffect(() => {
        if (result) {
            // チャートとレポートの描画処理
            renderChart(result.render_params);
            renderReport(result.report_params);
        }
    }, [result]);

    // リセット処理
    const resetForm = () => {
        // フォームのリセット処理
    };

    return (
        <div className="nash-calculator">
            {/* フォーム要素 */}
            <div className="form-group">
                {/* 入力フィールド */}
            </div>
            
            <button 
                onClick={calculateNash} 
                disabled={isLoading}
                className="btn btn-primary calculate"
            >
                計算する
            </button>
            
            <button 
                onClick={resetForm}
                className="btn btn-secondary"
                id="reset"
            >
                リセット
            </button>
            
            {isLoading && (
                <div id="nash_spinner" className="spinner-border" role="status">
                    <span className="sr-only">Loading...</span>
                </div>
            )}
            
            {error && (
                <div id="alert_danger" className="alert alert-danger">
                    {error}
                </div>
            )}
            
            {/* 結果表示エリア */}
            <div id="nash_result" className="result-area">
                {/* 結果がある場合に表示 */}
            </div>
            
            {/* チャート表示エリア */}
            <div id="chart_area_nash" style={{ display: result ? 'block' : 'none' }}>
                <canvas id="chart_nash_social_welfare"></canvas>
            </div>
        </div>
    );
};

export default NashCalculator;
```

### 3.2 React Hooks を使用したデータ取得

React では、カスタムフックを作成してデータ取得ロジックを分離することも可能です。

```tsx
// useNashCalculation.ts
import { useState } from 'react';
import axios from 'axios';

export const useNashCalculation = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [result, setResult] = useState<any>(null);

    const calculateNash = (data: any) => {
        setIsLoading(true);
        setError(null);
        
        return axios.post('/api/nash/calculate', data)
            .then(response => {
                setResult(response.data);
                return response.data;
            })
            .catch(error => {
                const errorMessage = error.response?.data || 'エラーが発生しました';
                setError(errorMessage);
                throw errorMessage;
            })
            .finally(() => {
                setIsLoading(false);
            });
    };

    return { calculateNash, isLoading, error, result };
};
```

## 4. jQuery から DOM 操作も置き換える場合

jQuery の AJAX だけでなく、DOM 操作も置き換える場合は以下のようになります：

```typescript
// jQuery の DOM 操作
$('#nash_result').html(tmpl);
$('#nash_spinner').hide();
$('button.calculate').removeClass('disabled');

// ネイティブ JavaScript の DOM 操作
document.getElementById('nash_result')!.innerHTML = tmpl;
document.getElementById('nash_spinner')!.style.display = 'none';
document.querySelector('button.calculate')?.classList.remove('disabled');

// スクロール処理
$("html,body").animate({scrollTop: offset});

// ネイティブ JavaScript のスクロール処理
window.scrollTo({
    top: offset,
    behavior: 'smooth'
});

// jQuery の each
$('#nash_result .katex_exp').each(function () {
    let element = $(this)[0];
    katex.render($(this).attr('expression'), element, {
        throwOnError: false
    });
});

// ネイティブ JavaScript の forEach
document.querySelectorAll('#nash_result .katex_exp').forEach(element => {
    katex.render(element.getAttribute('expression') || '', element, {
        throwOnError: false
    });
});
```

## まとめ

jQuery の AJAX 通信を置き換えるには、以下の選択肢があります：

1. **Fetch API** - モダンブラウザでサポートされているネイティブな API
2. **Axios** - より使いやすいプロミスベースの HTTP クライアント（すでにプロジェクトに導入済み）
3. **React** - コンポーネントベースのアプローチで、状態管理とUIの更新を統合的に扱える

React を導入する場合は、単にAJAX通信だけでなく、コンポーネント設計やステート管理なども考慮する必要があります。段階的に移行する場合は、まず Fetch API や Axios に置き換え、その後 React コンポーネントに移行するアプローチが推奨されます。