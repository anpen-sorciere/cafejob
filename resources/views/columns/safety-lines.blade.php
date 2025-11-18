@extends('layouts.app')

@section('title', 'コンカフェの安全・危険ラインとは')

@section('content')
<div class="container my-8 column-page">
    <article class="column-article card p-5">
        <h1 class="column-title mb-3">コンカフェの安全・危険ラインとは</h1>
        <p class="text-muted mb-4">
            「このお店、本当に大丈夫かな…？」<br>
            求人や面談だけでは分かりにくい部分を、<br>
            できるだけ分かりやすく整理しました。
        </p>

        {{-- 1. これは避けたほうがいい「危険ライン」例 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">1. これは避けたほうがいい「危険ライン」例</h2>

            <h3 class="column-subtitle mt-3 mb-1">(1) 給与やバックの説明が口頭だけ</h3>
            <ul>
                <li>書面やメッセージで残らない</li>
                <li>詳しく聞くと「とりあえず来てからで」と濁される</li>
            </ul>
            <p>
                → 後から条件を変えられたり、<br>
                「そんな話はしていない」と言われるリスクがあります。
            </p>

            <h3 class="column-subtitle mt-3 mb-1">(2) ノルマや罰金が異常に多い</h3>
            <ul>
                <li>飲み放題の売上ノルマ</li>
                <li>遅刻・当日欠勤の高額罰金</li>
                <li>シャンパンを入れないと時給ダウン　…など</li>
            </ul>
            <p>
                → プレッシャーが強すぎると、<br>
                心身ともにすり減りやすくなります。
            </p>

            <h3 class="column-subtitle mt-3 mb-1">(3) お酒や店外での同伴を強く迫られる</h3>
            <ul>
                <li>「飲めないと無理」「とりあえずイッキ」</li>
                <li>店外デートや個人ライン交換を半強制</li>
            </ul>
            <p>
                → トラブルに発展しやすく、<br>
                安全面でもリスクが高いサインです。
            </p>

            <h3 class="column-subtitle mt-3 mb-1">(4) キャストやお客様への暴言・ハラスメント</h3>
            <ul>
                <li>SNSでキャストを晒すような行為</li>
                <li>店内での暴言、人格否定</li>
                <li>お客様の迷惑行為を放置</li>
            </ul>
            <p>
                → こういった環境は、長く続けるには向きません。
            </p>
        </section>

        {{-- 2. 安心して働きやすい「安全ライン」の目安 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">2. 安心して働きやすい「安全ライン」の目安</h2>

            <h3 class="column-subtitle mt-3 mb-1">(1) ルールが文字で残っている</h3>
            <ul>
                <li>給与体系・バック・シフトルールが文章で説明される</li>
                <li>LINEやメールで条件を書き残してくれる</li>
            </ul>

            <h3 class="column-subtitle mt-3 mb-1">(2) キャストの安全を優先するスタンス</h3>
            <ul>
                <li>お酒の強要NG</li>
                <li>同伴・店外営業は「希望者のみ」など選択制</li>
                <li>トラブル時の対応フローが決まっている</li>
            </ul>

            <h3 class="column-subtitle mt-3 mb-1">(3) 無理なスケジュールを組ませない</h3>
            <ul>
                <li>体調不良時には休ませてくれる</li>
                <li>テスト期間や本業の都合に配慮してくれる</li>
            </ul>
        </section>

        {{-- 3. 実際に働いている子の声も参考に --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">3. 実際に働いている子の声も参考に</h2>
            <ul>
                <li>公式アカウントだけでなく、キャストのSNSもチェック</li>
                <li>「楽しい」だけでなく「しんどいこと」も正直に話してくれるか</li>
            </ul>
            <p>
                ただし、個人の相性もあるので、<br>
                SNSの情報はあくまで<strong>参考のひとつ</strong>として受け止めましょう。
            </p>
        </section>

        {{-- 4. 少しでも違和感を覚えたら --}}
        <section class="mb-2">
            <h2 class="column-section-title mb-2">4. 少しでも違和感を覚えたら</h2>
            <p>
                面談ややり取りの中で、
            </p>
            <ul>
                <li>話がコロコロ変わる</li>
                <li>質問にきちんと答えてもらえない</li>
                <li>「考える暇を与えずに」決断させようとする</li>
            </ul>
            <p>
                などの違和感があれば、<br>
                いったん持ち帰って冷静に考える時間を取りましょう。
            </p>
        </section>
    </article>
</div>
@endsection

