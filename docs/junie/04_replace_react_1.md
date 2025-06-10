```markdown
# jQuery から React への変換: reset ボタンの実装

## 現在の実装 (jQuery)

現在、`resources/js/pages/nash.ts` ファイルでは、reset ボタンのクリックイベントと reset 関数が jQuery を使って次のように実装されています:

```typescript
// jQuery イベントハンドラ
$('button#reset').click(function () {
    reset();
});

// reset 関数
function reset(): void {
    $('#nash_block input.form-control').each(function () {
        if (!$(this).attr('readonly')) {
            const default_val = $(this).attr('default_val') || '';
            $(this).val(default_val);
        }
    });
}
```

## React への変換

React では、DOM 操作を直接行うのではなく、状態（state）を通じて UI を更新します。以下に React での実装方法を示します。

### 1. React コンポーネントの作成

まず、Nash フォーム用の React コンポーネントを作成します:

```tsx
// NashForm.tsx
import React, { useState, useEffect } from 'react';
import { beforeCalculate } from "../functions/calculate";
import { doNashCalculate } from "../functions/nash";
import katex from "katex";

interface FormValues {
  alpha_1_numerator: string;
  alpha_1_denominator: string;
  alpha_2_numerator: string;
  alpha_2_denominator: string;
  beta_1_numerator: string;
  beta_1_denominator: string;
  beta_2_numerator: string;
  beta_2_denominator: string;
  rho_numerator: string;
  rho_denominator: string;
  [key: string]: string; // インデックスシグネチャ
}

const NashForm: React.FC = () => {
  // 初期値を保持するための状態
  const [defaultValues, setDefaultValues] = useState<FormValues>({
    alpha_1_numerator: '',
    alpha_1_denominator: '',
    alpha_2_numerator: '',
    alpha_2_denominator: '',
    beta_1_numerator: '',
    beta_1_denominator: '',
    beta_2_numerator: '',
    beta_2_denominator: '',
    rho_numerator: '',
    rho_denominator: '',
  });

  // 現在の入力値を保持するための状態
  const [formValues, setFormValues] = useState<FormValues>({...defaultValues});

  // コンポーネントのマウント時に初期値を取得
  useEffect(() => {
    // DOM から初期値を取得
    const initialValues: FormValues = {...defaultValues};
    
    document.querySelectorAll('#nash_block input.form-control').forEach((input) => {
      const element = input as HTMLInputElement;
      const id = element.id;
      const defaultVal = element.getAttribute('default_val') || '';
      
      if (id && !element.readOnly) {
        initialValues[id] = defaultVal;
      }
    });
    
    setDefaultValues(initialValues);
    setFormValues(initialValues);
    
    // KaTeX の初期化
    document.querySelectorAll('.form-layout .katex_exp').forEach((element) => {
      const exp = element.getAttribute('expression') || '';
      katex.render(exp, element as HTMLElement, {
        throwOnError: false
      });
    });
  }, []);

  // 入力値の変更を処理する関数
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { id, value } = e.target;
    setFormValues({
      ...formValues,
      [id]: value
    });
  };

  // 計算ボタンのクリックハンドラ
  const handleCalculate = () => {
    beforeCalculate('nash_spinner');
    doNashCalculate();
  };

  // リセットボタンのクリックハンドラ
  const handleReset = () => {
    // デフォルト値に戻す
    setFormValues({...defaultValues});
    
    // DOM の値も更新（React で完全に制御できない場合のフォールバック）
    Object.keys(defaultValues).forEach(id => {
      const element = document.getElementById(id) as HTMLInputElement;
      if (element && !element.readOnly) {
        element.value = defaultValues[id];
      }
    });
  };

  return (
    <div id="nash_block" className="tab_form card pd-20 mg-t-20">
      {/* 既存の HTML 構造をここに配置し、value と onChange を追加 */}
      {/* ... */}
      
      <div className="col-sm-6 col-md-3 simulate_player">
        <button 
          className="btn btn-primary mg-b-10 calculate"
          onClick={handleCalculate}
        >
          Calculate
        </button>
        <button 
          id="reset" 
          className="btn btn-secondary mg-b-10"
          onClick={handleReset}
        >
          Reset
        </button>
      </div>
      
      {/* ... */}
    </div>
  );
};

export default NashForm;
```

### 2. 既存のコードを修正する場合の最小限の変更

完全な React コンポーネントへの移行が難しい場合は、既存の jQuery コードを最小限の変更で React 化することもできます:

```typescript
// nash.ts
import { beforeCalculate } from "../functions/calculate";
import { doNashCalculate } from "../functions/nash";
import katex from "katex";
import React from 'react';
import ReactDOM from 'react-dom';

// React コンポーネント
const ResetButton: React.FC = () => {
  const handleReset = () => {
    // 既存の reset 関数と同じロジック
    document.querySelectorAll('#nash_block input.form-control').forEach((input) => {
      const element = input as HTMLInputElement;
      if (!element.readOnly) {
        const defaultVal = element.getAttribute('default_val') || '';
        element.value = defaultVal;
      }
    });
  };

  return (
    <button 
      id="reset" 
      className="btn btn-secondary mg-b-10"
      onClick={handleReset}
    >
      Reset
    </button>
  );
};

// 既存の jQuery コード（reset ボタン以外）
$(function(){
    $('.simulate_player button.calculate').click(function () {
        if (!$(this).hasClass('disabled')) {
            beforeCalculate('nash_spinner');
            doNashCalculate();
        }
    });

    // reset ボタンの jQuery コードは削除

    $('.form-layout .katex_exp').each(function () {
        let element = $(this)[0];
        katex.render($(this).attr('expression') || '', element, {
            throwOnError: false
        });
    });
    
    // Reset ボタンを React でレンダリング
    const resetButtonContainer = document.getElementById('reset-button-container');
    if (resetButtonContainer) {
        const root = ReactDOM.createRoot(resetButtonContainer);
        root.render(<ResetButton />);
    }
});

// reset 関数は不要になるため削除
```

この場合、HTML 側で reset ボタンを配置する場所に `<div id="reset-button-container"></div>` を追加する必要があります。

## 実装上の注意点

### jQuery と React の違い

1. **イベント処理**: jQuery は DOM 要素に直接イベントリスナーを追加しますが、React は仮想 DOM を使用し、宣言的に UI を記述します。

2. **状態管理**: jQuery では DOM 要素の値を直接操作しますが、React では状態（state）を通じて UI を更新します。

3. **パフォーマンス**: React の仮想 DOM は効率的な更新を可能にし、大規模なアプリケーションでより良いパフォーマンスを発揮します。

### 移行時の考慮点

- 完全な React 化が難しい場合は、段階的に移行することも可能です。
- jQuery と React を混在させる場合は、DOM 操作の競合に注意が必要です。
- React コンポーネントは再利用可能なため、同様のフォームが他の場所にもある場合は共通コンポーネントとして設計すると良いでしょう。

この実装により、reset ボタンの機能を jQuery から React に移行することができます。
```
