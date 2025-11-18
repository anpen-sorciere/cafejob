@extends('layouts.app')

@section('title', 'コンカフェで"長く楽しく"働くコツ')

@section('content')
<div class="container my-8 column-page">
    <article class="column-article card p-5">
        <h1 class="column-title mb-3">コンカフェで"長く楽しく"働くコツ</h1>
        <p class="text-muted mb-4">
            せっかく始めたコンカフェの仕事、<br>
            できれば「短期で燃え尽き」ではなく、<br>
            気持ちよく続けられたら嬉しいですよね。<br>
            ここでは、現場感覚も交えながら、長く楽しく続けるためのコツをまとめました。
        </p>

        {{-- 1. 自分の「働くライン」を決めておく --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">1. 自分の「働くライン」を決めておく</h2>
            <ul>
                <li>週に何日まで働くか</li>
                <li>何時までなら無理なく働けるか</li>
                <li>お酒はどれくらいまでなら大丈夫か</li>
            </ul>
            <p>
                先に自分の中の「ルール」を決めておくと、<br>
                流されにくくなります。
            </p>
        </section>

        {{-- 2. お金との付き合い方 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">2. お金との付き合い方</h2>
            <p>
                「今日これだけ稼げたから、全部使っちゃおう！」<br>
                を続けると、あっという間に疲れてしまいます。
            </p>
            <ul>
                <li>収入のうち、いくらを貯金に回すか</li>
                <li>いくらを自分のご褒美や推し活に使うか</li>
            </ul>
            <p>
                ざっくりでもいいので、<br>
                お金の"ふり先"を決めておくと安心です。
            </p>
        </section>

        {{-- 3. 同僚キャストとの距離感 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">3. 同僚キャストとの距離感</h2>
            <ul>
                <li>仲良くなるのはとても大事</li>
                <li>でも、距離が近すぎると疲れることも</li>
            </ul>
            <p>
                プライベートまで全部共有しなくてもOK。<br>
                適度な距離感で「仕事の仲間」として<br>
                気持ちよく付き合える関係を目指しましょう。
            </p>
        </section>

        {{-- 4. メンタルケアとオフの時間 --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">4. メンタルケアとオフの時間</h2>
            <ul>
                <li>休みの日は、あえてお店のことを考えない時間を作る</li>
                <li>好きなものに浸かる「完全オフ日」を用意する</li>
                <li>しんどいときに話せる相手（友達・家族・信頼できるスタッフ）を持つ</li>
            </ul>
            <p>
                「ずっとオンの状態」が続くと、<br>
                どんな仕事でもメンタルが消耗してしまいます。
            </p>
        </section>

        {{-- 5. お店とのコミュニケーション --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">5. お店とのコミュニケーション</h2>
            <ul>
                <li>体調不良のときは早めに相談する</li>
                <li>しんどいこと・合わないことも、伝え方を選びつつ共有する</li>
                <li>無理に「大丈夫です！」だけ言い続けない</li>
            </ul>
            <p>
                話せる範囲で正直に伝えることで、<br>
                環境を調整してもらえることもあります。
            </p>
        </section>

        {{-- 6. 定期的に「続けるか／変えるか」を見直す --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">6. 定期的に「続けるか／変えるか」を見直す</h2>
            <p>
                学校や本業の状況が変わった<br>
                生活リズムが変わった<br>
                自分の目標が変わった
            </p>
            <p>
                こういったタイミングで、
            </p>
            <ul>
                <li>このお店で続ける？</li>
                <li>働き方を変える？</li>
                <li>一度区切りをつける？</li>
            </ul>
            <p>
                を見直していくことも大切です。
            </p>
        </section>

        {{-- 7. 自分を大切にしながら働く --}}
        <section class="mb-2">
            <h2 class="column-section-title mb-2">7. 自分を大切にしながら働く</h2>
            <p>
                コンカフェの仕事は、<br>
                お客様を楽しませることがメインですが、
            </p>
            <ul>
                <li>自分の体</li>
                <li>自分の時間</li>
                <li>自分の心</li>
            </ul>
            <p>
                を犠牲にしすぎてしまうと、<br>
                どこかで無理が出てしまいます。
            </p>
            <blockquote class="blockquote my-3">
                <p class="mb-0">「自分を大事にすることが、<br>
                結果的にお店やお客様のためにもなる」</p>
            </blockquote>
            <p>
                その感覚を忘れずに、<br>
                自分のペースで"長く楽しく"続けていきましょう。
            </p>
        </section>
    </article>
</div>
@endsection

