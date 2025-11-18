@extends('layouts.app')

@section('title', 'はじめてのカフェコレガイド')
@section('description', 'コンカフェのお仕事が初めての方向けに、お店選びのポイントや応募〜勤務開始までの流れをまとめました。')

@section('content')
<div class="container my-8 guide-page">
    {{-- ヒーロー部分 --}}
    <div class="guide-hero card px-5 py-6 mb-6">
        <h1 class="guide-title mb-3">はじめてのカフェコレガイド</h1>
        <p class="text-muted">
            コンカフェのお仕事に興味があるけれど、
            「どんなお店を選べばいいの？」「応募したあとってどうなるの？」
            そんな"はじめてさん"向けに、安心して働けるためのポイントをまとめました。
        </p>
    </div>

    {{-- 1. コンカフェってどんなお仕事？ --}}
    <section class="card p-4 mb-5 guide-section">
        <h2 class="section-title mb-3">1. コンカフェってどんなお仕事？</h2>
        <p>
            コンセプトカフェ（通称：コンカフェ）は、
            「メイド」「アイドル」「執事」「アニメ系」など、
            テーマに合わせて接客する"エンタメ性のあるお仕事"です。
        </p>
        <h3 class="mt-3 mb-2">主なお仕事</h3>
        <ul>
            <li>お客様へのお席案内・接客</li>
            <li>ドリンク・フードの提供</li>
            <li>チェキ撮影・イベント対応（ある店舗のみ）</li>
            <li>SNS投稿などの活動（店舗により異なる）</li>
        </ul>
        <h3 class="mt-4 mb-2">よくある疑問</h3>
        <dl class="qa-list">
            <dt>Q. 経験がなくても大丈夫？</dt>
            <dd>A. ほとんどのお店が未経験歓迎。先輩がついて教えてくれるお店も多いです。</dd>
            <dt class="mt-2">Q. 容姿に自信がない…</dt>
            <dd>A. 「世界観に合うかどうか」が大事。メイク・衣装の相談に乗ってくれるお店もあります。</dd>
        </dl>
    </section>

    {{-- 2. はじめてのお店選びで大事な3つのポイント --}}
    <section class="card p-4 mb-5 guide-section">
        <h2 class="section-title mb-3">2. はじめてのお店選びで大事な3つのポイント</h2>
        <div class="guide-points row g-3">
            <article class="guide-point card-soft p-3 col-md-4">
                <h3 class="mb-2">🔎 POINT 1：ジャンル（世界観）が合うか</h3>
                <p>
                    メイド、アイドル、執事、コスプレ、カジュアル系…コンカフェと一言で言っても雰囲気は全然違います。
                    「ここなら自分らしくいられそう」と思える世界観を選びましょう。
                </p>
            </article>
            <article class="guide-point card-soft p-3 col-md-4">
                <h3 class="mb-2">💰 POINT 2：給与体系が分かりやすい</h3>
                <p>
                    時給の幅、各種バック、交通費、昇給のルールなど、
                    給与ページがシンプルで分かりやすく書かれているかはとても重要なチェックポイントです。
                </p>
            </article>
            <article class="guide-point card-soft p-3 col-md-4">
                <h3 class="mb-2">🔒 POINT 3：安心して働ける環境か</h3>
                <p>
                    SNS更新状況、口コミ、応募後の対応、面談の丁寧さなどを確認して、
                    「ここなら安心して働けそう」と思えるかどうかを大切にしましょう。
                </p>
            </article>
        </div>
    </section>

    {{-- 3. 応募〜実際に働き始めるまでの流れ --}}
    <section class="card p-4 mb-5 guide-section">
        <h2 class="section-title mb-3">3. 応募〜実際に働き始めるまでの流れ</h2>
        <ol class="step-list">
            <li>
                <strong>STEP 1：気になるお店を探す</strong><br>
                カフェコレでは「ジャンル」「エリア」「時給」「雰囲気」から探せます。
                複数のお店を候補としてキープしておくのもおすすめです。
            </li>
            <li>
                <strong>STEP 2：応募する</strong><br>
                応募フォームから、名前や希望勤務スタイル、連絡先、質問したいことなどを送ります。
                応募後の対応が丁寧かどうかも、お店選びの判断材料になります。
            </li>
            <li>
                <strong>STEP 3：面談（＋必要に応じて見学）</strong><br>
                多くのお店では、まず面談でお互いの希望を確認します。
                仕事内容・シフト・ルール説明、不安な点の相談などをしっかり聞いておきましょう。
            </li>
            <li>
                <strong>STEP 4：お互いがOKなら、通常勤務スタート</strong><br>
                面談の内容に納得でき、お店側とも条件が合えば、初回の出勤日を決めて通常勤務としてスタートします。
            </li>
        </ol>
        <p class="text-muted mt-3">
            ※いわゆる「体験入店（体入）」を行わないお店も多くあります。
            しっかり面談したうえで通常勤務から始めるスタイルは、
            キャスト・お店双方にとって安心な方法のひとつです。
        </p>
    </section>

    {{-- 4. はじめてさんが安心するチェックリスト --}}
    <section class="card p-4 mb-5 guide-section">
        <h2 class="section-title mb-3">4. はじめてさんが安心するチェックリスト</h2>
        <ul class="checklist">
            <li>給与・バックのルールが明確</li>
            <li>自分に合いそうな世界観・客層だと思える</li>
            <li>SNSやサイトの情報が更新されている</li>
            <li>応募〜面談のやり取りが丁寧</li>
            <li>無理な勧誘・強引な条件がない</li>
            <li>何かあったときの相談先がはっきりしている</li>
        </ul>
    </section>

    {{-- 5. カフェコレの安心ポイント --}}
    <section class="card p-4 mb-5 guide-section">
        <h2 class="section-title mb-3">5. カフェコレの安心ポイント</h2>
        <p>
            カフェコレでは、はじめての方でも安心してお店を探せるように、次のような工夫をしていきます。
        </p>
        <ul>
            <li>パッと見て分かりやすい求人レイアウト</li>
            <li>世界観が伝わる店舗プロフィール情報</li>
            <li>給与や待遇をできるだけシンプルに表示</li>
            <li>特集・コラムで「失敗しないお店選び」をサポート</li>
            <li>体験入店の有無にかかわらず、安心して働ける環境づくりを重視</li>
        </ul>
        <p class="mt-3">
            あなたにとって"長く楽しく働けるコンカフェ"が見つかりますように。
        </p>
    </section>
</div>
@endsection

