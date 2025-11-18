@extends('layouts.app')

@section('title', 'SNSの使い方と身バレ対策')

@section('content')
<div class="container my-8 column-page">
    <article class="column-article card p-5">
        <h1 class="column-title mb-3">SNSの使い方と身バレ対策</h1>
        <p class="text-muted mb-4">
            コンカフェのお仕事では、<br>
            SNS が「お店のPR」かつ「自分の武器」になります。<br>
            一方で、使い方を間違えると身バレのリスクも。<br>
            ここでは、基本的な考え方と具体的な対策をまとめました。
        </p>

        {{-- 1. アカウントは仕事用とプライベートを分ける --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">1. アカウントは仕事用とプライベートを分ける</h2>
            <ul>
                <li><strong>仕事用</strong>：源氏名・お店名で運用</li>
                <li><strong>プライベート</strong>：本名や友達関係が分かるもの</li>
            </ul>
            <p>
                <strong>絶対に混ぜない</strong>ことが、身バレ防止の大前提です。
            </p>
        </section>

        {{-- 2. 投稿するときのチェックポイント --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">2. 投稿するときのチェックポイント</h2>

            <h3 class="column-subtitle mt-3 mb-1">NGになりやすいもの</h3>
            <ul>
                <li>職場・学校・自宅が分かる風景写真</li>
                <li>本名が写っている書類や名札</li>
                <li>学校の制服、社員証</li>
            </ul>

            <h3 class="column-subtitle mt-3 mb-1">位置情報のオフ</h3>
            <ul>
                <li>自動で位置情報がつく設定はオフに</li>
                <li>「◯◯駅なう」などリアルタイムでの位置投稿は控える</li>
            </ul>
        </section>

        {{-- 3. 顔出し・加工のルール --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">3. 顔出し・加工のルール</h2>
            <p>
                お店によって、
            </p>
            <ul>
                <li>顔出し必須</li>
                <li>マスクOK</li>
                <li>スタンプで一部隠すのはNG など</li>
            </ul>
            <p>
                ルールが違います。
            </p>
            <p>
                自分がどこまで出せるか・出したくないかを<br>
                面談時に正直に相談し、すり合わせておきましょう。
            </p>
        </section>

        {{-- 4. DM・個人連絡の扱い --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">4. DM・個人連絡の扱い</h2>
            <ul>
                <li>お客様とは基本的にお店経由の連絡先を使う</li>
                <li>個人LINEや本垢のSNSは教えない</li>
                <li>不快なDMが来たらスクショを残してスタッフに相談</li>
            </ul>
            <p>
                「仲良くなったからいいや」と境界線を曖昧にすると、<br>
                後からトラブルになりやすいです。
            </p>
        </section>

        {{-- 5. 写真を撮るときの注意 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">5. 写真を撮るときの注意</h2>
            <ul>
                <li>他のキャストやお客様が勝手に映り込んでいないか</li>
                <li>店内の掲示物に個人情報が写っていないか</li>
                <li>顔を隠すときも、雑になりすぎない（不快に見えないように）</li>
            </ul>
            <p>
                撮る前に周りを軽く確認する習慣をつけると、安全度が上がります。
            </p>
        </section>

        {{-- 6. ブロック・ミュートをためらわない --}}
        <section class="mb-2">
            <h2 class="column-section-title mb-2">6. ブロック・ミュートをためらわない</h2>
            <ul>
                <li>明らかに不快・危険なアカウント</li>
                <li>しつこくプライベートを聞いてくる人</li>
                <li>誹謗中傷アカウント</li>
            </ul>
            <p>
                こういったアカウントは、<br>
                遠慮なくブロック・ミュートしてOKです。
            </p>
        </section>
    </article>
</div>
@endsection

