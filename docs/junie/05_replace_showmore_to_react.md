
# jQuery showMore プラグインの React コンポーネント置き換え計画

## 現状分析

現在の実装では、jQuery の showMore プラグインが以下のように使用されています：

1. `resources/js/plugins/show-more-wrapper.ts` でプラグインをラップしています
2. `resources/js/functions/centipede.js` の `renderCentipedeReportArea` 関数内で、`.showmore_block` クラスを持つ要素に対して適用されています
3. プラグインは要素の高さが指定値（デフォルト: 300px）を超える場合、超過部分を隠し、「show more」ボタンを表示します
4. ボタンクリック時にアニメーションと共に全体を表示し、「show less」ボタンに切り替わります
5. 「show less」ボタンクリック時に元の状態に戻ります

## 対応計画

### 1. React コンポーネントの作成

新しい React コンポーネント `ShowMore` を作成します：

```tsx
// resources/js/components/ShowMore.tsx
import React, { useState, useRef, useEffect } from 'react';
import ReactDOM from 'react-dom';

interface ShowMoreProps {
  children: React.ReactNode;
  minHeight?: number;
  buttonTextMore?: string;
  buttonTextLess?: string;
  buttonClassName?: string;
  animationSpeed?: number;
  className?: string;
}

const ShowMore: React.FC<ShowMoreProps> = ({
  children,
  minHeight = 300,
  buttonTextMore = 'show more',
  buttonTextLess = 'show less',
  buttonClassName = 'showmore-button',
  animationSpeed = 300,
  className = '',
}) => {
  const [expanded, setExpanded] = useState(false);
  const [showButton, setShowButton] = useState(false);
  const contentRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    // 要素の高さをチェックしてボタンの表示/非表示を決定
    if (contentRef.current) {
      const fullHeight = contentRef.current.scrollHeight;
      setShowButton(fullHeight > minHeight);
    }
  }, [minHeight, children]);

  const toggleExpand = () => {
    setExpanded(!expanded);
  };

  const contentStyle = {
    minHeight: minHeight,
    maxHeight: expanded ? 'none' : `${minHeight}px`,
    overflow: 'hidden',
    transition: `max-height ${animationSpeed}ms ease-in-out`,
  };

  return (
    <div className={`showmore-wrapper ${className}`}>
      <div ref={contentRef} style={contentStyle}>
        {children}
      </div>
      
      {showButton && (
        <button
          type="button"
          className={buttonClassName}
          aria-expanded={expanded}
          onClick={toggleExpand}
        >
          {expanded ? buttonTextLess : buttonTextMore}
        </button>
      )}
    </div>
  );
};

export default ShowMore;
```

### 2. ラッパーの更新

`show-more-wrapper.ts` を更新して React コンポーネントを使用するようにします：

```tsx
// resources/js/plugins/show-more-wrapper.ts
import React from 'react';
import ReactDOM from 'react-dom';
import ShowMore from '../components/ShowMore';

interface ShowMoreOptions {
  minheight?: number;
  buttontxtmore?: string;
  buttontxtless?: string;
  buttoncss?: string;
  animationspeed?: number;
}

export default {
  init: function(selector: string | JQuery, options: ShowMoreOptions = {}) {
    const elements = typeof selector === 'string' ? document.querySelectorAll(selector) : $(selector).toArray();
    
    elements.forEach(element => {
      // 既に処理済みかチェック
      const id = element.id || `showmore-${Math.random().toString(36).substr(2, 9)}`;
      if (document.getElementById(`showmore-${id}`)) {
        return;
      }
      
      // React コンポーネントをレンダリング
      const container = document.createElement('div');
      container.id = `showmore-${id}`;
      element.parentNode?.insertBefore(container, element);
      container.appendChild(element);
      
      ReactDOM.render(
        <ShowMore
          minHeight={options.minheight || 300}
          buttonTextMore={options.buttontxtmore || 'show more'}
          buttonTextLess={options.buttontxtless || 'show less'}
          buttonClassName={options.buttoncss || 'showmore-button'}
          animationSpeed={options.animationspeed || 300}
        >
           {/* 既存 HTML を文字列で渡す */}
           <div dangerouslySetInnerHTML={{ __html: element.innerHTML }} />
        </ShowMore>,
        container
      );
    });
    
    // 互換性のために jQuery オブジェクトを返す
    return typeof selector === 'string' ? $(selector) : selector;
  }
};
```

### 3. CSS スタイルの追加

必要なスタイルを追加します：

```scss
// resources/scss/components/_show-more.scss
.showmore-wrapper {
  position: relative;
  width: 100%;
  
  .showmore-button {
    cursor: pointer;
    text-align: center;
    padding: 10px;
    margin-top: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
    
    &:hover {
      background-color: #e9ecef;
    }
  }
}
```

### 4. 段階的な移行計画

1. **フェーズ 1: 並行実装**
    - 新しい React コンポーネントを実装しつつ、既存の jQuery プラグインも維持

2. **フェーズ 2: テストと検証**
    - 新しい React 実装をテスト環境でテスト
    - 既存の jQuery 実装と比較して、動作や見た目に問題がないか確認

3. **フェーズ 3: 完全移行**
    - jQuery プラグインへの依存を完全に削除
    - ラッパーを React 実装のみを使用するように更新

## 技術的考慮事項

### アニメーション

jQuery 実装では `.animate()` メソッドを使用していますが、React 実装では CSS トランジションを使用します。これにより：
- パフォーマンスが向上（CSS アニメーションは GPU アクセラレーションを利用可能）
- コードがシンプルになる

### DOM 操作

jQuery 実装では直接 DOM を操作していますが、React 実装では仮想 DOM を使用します：
- React の状態管理を活用して要素の表示/非表示を制御
- `useRef` フックを使用して DOM 要素の測定を行う

### 互換性

- 既存のコードとの互換性を保つため、同じ CSS クラス名とオプション名を使用
- jQuery プラグインと同様の API を提供するラッパーを維持

## まとめ

この計画に従って jQuery の showMore プラグインを React コンポーネントに置き換えることで、以下のメリットが得られます：

1. モダンな React ベースのコードベースへの移行を促進
2. コンポーネントベースのアプローチによる再利用性と保守性の向上
3. TypeScript の型安全性の活用
4. パフォーマンスの向上（特にアニメーションにおいて）

また、段階的な移行アプローチにより、リスクを最小限に抑えながら安全に移行を進めることができます。

## 注意事項

- この計画は、junieに生成を依頼しましたが、その後細かい箇所の調整を行いました。
- この計画に基づいて置き換え作業を行う際は、記載内容に不備がないか再度の確認を行なって下さい。