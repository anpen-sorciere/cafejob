@extends('layouts.app')

@section('title', '面談で聞くべき5つの質問')

@section('content')
<div class="container my-8 column-page">
    <article class="column-article card p-5">
        <h1 class="column-title mb-3">面談で聞くべき5つの質問</h1>
        <p class="text-muted mb-4">
            お店選びで一番大事なのは、「面談（面接）のときに何を聞くか」です。<br>
            ここでは、最低限聞いておきたい5つの質問をまとめました。
        </p>

        {{-- 1. 給与とバックの仕組みについて --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">1. 給与とバックの仕組みについて</h2>
            <blockquote class="blockquote my-3">
                <p class="mb-0">「時給と、バックの種類・金額を教えてください」</p>
            </blockquote>
            <ul>
                <li>基本時給はいくらか</li>
                <li>バックの対象（ドリンク／チェキ／ボトル…）</li>
                <li>バック率 or 1件あたりいくらか</li>
                <li>ノルマやペナルティがあるか</li>
            </ul>
            <p>
                ここが曖昧なまま入店すると、<br>
                後から「思っていたのと違う…」になりやすいポイントです。
            </p>
        </section>

        {{-- 2. シフトの決め方と出勤ペース --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">2. シフトの決め方と出勤ペース</h2>
            <blockquote class="blockquote my-3">
                <p class="mb-0">「週にどれくらい出られると助かりますか？」</p>
                <p class="mb-0">「テスト期間や本業が忙しいときの調整はできますか？」</p>
            </blockquote>
            <ul>
                <li>最低出勤日数（週◯日〜）</li>
                <li>1日の勤務時間（何時間からOKか）</li>
                <li>繁忙期やイベント時の出勤のお願い</li>
                <li>スケジュール調整の柔軟さ</li>
            </ul>
            <p>
                自分の生活リズムと合うかをイメージしながら聞きましょう。
            </p>
        </section>

        {{-- 3. お酒・SNS・店外活動のルール --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">3. お酒・SNS・店外活動のルール</h2>
            <blockquote class="blockquote my-3">
                <p class="mb-0">「お酒の強制はありませんか？」</p>
                <p class="mb-0">「SNSの運用ルールを教えてください」</p>
                <p class="mb-0">「店外で会うような営業は必要ですか？」</p>
            </blockquote>
            <ul>
                <li>飲めない場合の対応</li>
                <li>SNSに載せてOKな内容／NGな内容</li>
                <li>同伴／アフター／店外デートの扱い</li>
            </ul>
            <p>
                ここで説明が曖昧だったり、<br>
                「とりあえず入ってから考えよう」と濁される場合は慎重に。
            </p>
        </section>

        {{-- 4. トラブルが起きたときの対応 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">4. トラブルが起きたときの対応</h2>
            <blockquote class="blockquote my-3">
                <p class="mb-0">「もしお客様とトラブルになった場合、どのように対応されていますか？」</p>
            </blockquote>
            <ul>
                <li>スタッフが間に入ってくれるか</li>
                <li>出禁・注意などの基準があるか</li>
                <li>クレームが入ったときの対応方針</li>
            </ul>
            <p>
                キャストを守るスタンスがあるお店かどうかは、<br>
                この質問でかなり見えてきます。
            </p>
        </section>

        {{-- 5. 実際に働いている子の雰囲気 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">5. 実際に働いている子の雰囲気</h2>
            <blockquote class="blockquote my-3">
                <p class="mb-0">「どんなタイプのキャストさんが多いですか？」</p>
                <p class="mb-0">「長く続けている方はどんな方ですか？」</p>
            </blockquote>
            <ul>
                <li>年齢層</li>
                <li>雰囲気（ゆるめ／しっかり系／オタク寄り etc.）</li>
                <li>在籍している子の継続期間</li>
            </ul>
            <p>
                「長く続いている子が多いお店」は、<br>
                環境としても安定していることが多いです。
            </p>
        </section>

        {{-- メモを取りながら聞くのがおすすめ --}}
        <section class="mb-2">
            <h2 class="column-section-title mb-2">メモを取りながら聞くのがおすすめ</h2>
            <p>
                面談のときは緊張して、<br>
                家に帰るころには細かい話を忘れてしまいがちです。
            </p>
            <ul>
                <li>スマホのメモアプリ</li>
                <li>小さいメモ帳</li>
            </ul>
            <p>
                などにポイントだけ書き残しておくと、<br>
                複数のお店を比較するときにも役立ちます。
            </p>
        </section>
    </article>
</div>
@endsection

