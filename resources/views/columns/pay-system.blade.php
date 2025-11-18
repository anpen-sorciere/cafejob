@extends('layouts.app')

@section('title', 'コンカフェの給与体系')

@section('content')
<div class="container my-8 column-page">
    <article class="column-article card p-5">
        <h1 class="column-title mb-3">コンカフェの給与体系</h1>
        <p class="text-muted mb-4">
            コンカフェの求人を見ると、<br>
            「時給◯◯円〜」「バック 40%」「ドリンクバックあり」「罰金・ペナルティあり」<br>
            など、独特の言葉がたくさん出てきます。<br>
            ここでは、できるだけシンプルに給与体系を整理します。
        </p>

        {{-- 1. 基本は「時給＋各種バック」 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">1. 基本は「時給＋各種バック」</h2>
            <p>
                多くのコンカフェは、
            </p>
            <blockquote class="blockquote my-3">
                <p class="mb-0"><strong>基本時給 ＋ バック（歩合）</strong></p>
            </blockquote>
            <p>
                という形をとっています。
            </p>

            <h3 class="column-subtitle mt-3 mb-1">基本時給</h3>
            <ul>
                <li>出勤しているだけでもらえる固定のお金</li>
                <li>例：時給 1,300円 など</li>
            </ul>

            <h3 class="column-subtitle mt-3 mb-1">バック（歩合）</h3>
            <ul>
                <li>お客様のオーダーに応じて、売上の一部がキャストに還元される仕組み</li>
                <li>例：ドリンク・チェキ・シャンパンなど</li>
            </ul>
        </section>

        {{-- 2. よくあるバックの種類 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">2. よくあるバックの種類</h2>
            <p>
                お店によって名称は違いますが、代表的なものは次の通りです。
            </p>
            <ul>
                <li><strong>乾杯ドリンク</strong>：お客様と一緒に飲む1杯目のドリンク</li>
                <li><strong>チェキ／写メ</strong>：ツーショット・ピンショットなど</li>
                <li><strong>ボトル・シャンパン</strong>：単価が高いぶんバック率も高め</li>
                <li><strong>ゲーム・オプション</strong>：ゲーム1回毎、席延長など</li>
            </ul>

            <h3 class="column-subtitle mt-3 mb-1">バック率の例</h3>
            <ul>
                <li>ドリンク：売上の30〜40%</li>
                <li>シャンパン：売上の10〜25%</li>
                <li>チェキ：1枚につき ◯円固定 など</li>
            </ul>
            <p>
                求人に書かれていなくても、<br>
                面談のときに「バックの種類と金額」を具体的に聞いてOKです。
            </p>
        </section>

        {{-- 3. 「バック込み時給◯◯円〜」のカラクリ --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">3. 「バック込み時給◯◯円〜」のカラクリ</h2>
            <p>
                よくある表現に、
            </p>
            <blockquote class="blockquote my-3">
                <p class="mb-0">バック込みで時給3,000円以上も可能！</p>
            </blockquote>
            <p>
                というものがあります。
            </p>
            <p>
                これは「理論上、●●円稼げることもあるよ」という意味であって、<br>
                <strong>必ずその金額が保証されるわけではありません。</strong>
            </p>
            <ul>
                <li>平均するとどれくらいになるのか</li>
                <li>バックがつかない時間帯はどれくらいあるのか</li>
            </ul>
            <p>
                などを、現実的なラインで確認しておきましょう。
            </p>
        </section>

        {{-- 4. サンプル計算：1日4時間働いた場合 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">4. サンプル計算：1日4時間働いた場合</h2>
            <p>
                例として、
            </p>
            <ul>
                <li>時給 1,300円</li>
                <li>ドリンクバック 40%</li>
                <li>チェキバック 50%</li>
                <li>4時間勤務</li>
            </ul>
            <p>
                この条件で、1日のお客様オーダーが
            </p>
            <ul>
                <li>ドリンク 5杯（単価 1,000円）</li>
                <li>チェキ 3枚（単価 1,000円）</li>
            </ul>
            <p>
                だった場合：
            </p>

            <h3 class="column-subtitle mt-3 mb-1">時給ぶん</h3>
            <p>1,300円 × 4時間 = <strong>5,200円</strong></p>

            <h3 class="column-subtitle mt-3 mb-1">ドリンクバック</h3>
            <p>1,000円 × 5杯 × 40% = <strong>2,000円</strong></p>

            <h3 class="column-subtitle mt-3 mb-1">チェキバック</h3>
            <p>1,000円 × 3枚 × 50% = <strong>1,500円</strong></p>

            <p class="mt-3">
                <strong>合計：5,200 + 2,000 + 1,500 = 8,700円</strong>
            </p>
            <p>
                ＝ 4時間で実質「時給 2,175円」 くらいのイメージになります。
            </p>
        </section>

        {{-- 5. 気をつけたい項目 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">5. 気をつけたい項目</h2>

            <h3 class="column-subtitle mt-3 mb-1">① ノルマ・ペナルティ</h3>
            <ul>
                <li>「ドリンク◯杯に達しないと時給カット」</li>
                <li>「遅刻1回につき罰金◯円」</li>
                <li>「売上目標に届かないと出勤を減らされる」</li>
            </ul>
            <p>
                といったルールが強すぎるお店は要注意です。
            </p>

            <h3 class="column-subtitle mt-3 mb-1">② 体験入店の「日給」だけを見ない</h3>
            <p>
                体験入店の日給が高くても、
            </p>
            <ul>
                <li>本採用後の時給はどうなるのか</li>
                <li>バック率は変わらないのか</li>
            </ul>
            <p>
                を必ずセットで確認しましょう。
            </p>
        </section>

        {{-- 6. 給与体系を見るときのチェックリスト --}}
        <section class="mb-2">
            <h2 class="column-section-title mb-2">6. 給与体系を見るときのチェックリスト</h2>
            <ul>
                <li>基本時給はいくらか</li>
                <li>バックの種類と金額が明確か</li>
                <li>ノルマやペナルティがないか（あるなら内容）</li>
                <li>給与の支払いタイミング（日払い／月払いなど）</li>
                <li>明細や売上の内訳を確認できるか</li>
            </ul>
            <p>
                「高時給」よりも、<br>
                <strong>仕組みがシンプルで説明が丁寧なお店</strong>のほうが、<br>
                結果としてストレス少なく働けることが多いです。
            </p>
        </section>
    </article>
</div>
@endsection

