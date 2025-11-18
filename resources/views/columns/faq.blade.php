@extends('layouts.app')

@section('title', 'コンカフェでよくある質問（Q&A）')

@section('content')
<div class="container my-8 column-page">
    <article class="column-article card p-5">
        <h1 class="column-title mb-3">コンカフェでよくある質問（Q&A）</h1>
        <p class="text-muted mb-4">
            初めてコンカフェで働こうとすると、<br>
            みんな同じような不安や疑問を持ちます。<br>
            ここでは、よくある質問を Q&A 形式でまとめました。
        </p>

        {{-- Q1. 未経験でも本当に大丈夫？ --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">Q1. 未経験でも本当に大丈夫？</h2>
            <p>
                <strong>A. 大丈夫です。</strong>
            </p>
            <p>
                多くのお店が「未経験歓迎」としていて、実際に未経験スタートの子がほとんどです。
            </p>
            <p>
                大切なのは、
            </p>
            <ul>
                <li>仕事の説明や研修があるか</li>
                <li>分からないことを聞きやすい雰囲気か</li>
            </ul>
            <p>
                といった「受け入れ体制」のほうです。
            </p>
        </section>

        {{-- Q2. 見た目に自信がないけど大丈夫？ --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">Q2. 見た目に自信がないけど大丈夫？</h2>
            <p>
                <strong>A. 「量産型じゃないとダメ」なんてことはありません。</strong>
            </p>
            <p>
                コンカフェは、
            </p>
            <ul>
                <li>かわいい系</li>
                <li>クール系</li>
                <li>地雷系</li>
                <li>オタク系</li>
                <li>ボーイッシュ　…など</li>
            </ul>
            <p>
                いろんなタイプの子が活躍できる場所です。
            </p>
            <p>
                「お店のコンセプトと雰囲気に合っているか」<br>
                「お客様を楽しませようとする気持ちがあるか」<br>
                の方がずっと大切です。
            </p>
        </section>

        {{-- Q3. お酒が飲めないと無理？ --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">Q3. お酒が飲めないと無理？</h2>
            <p>
                <strong>A. 飲めない子でも働けるお店はたくさんあります。</strong>
            </p>
            <ul>
                <li>ソフトドリンクで乾杯OK</li>
                <li>ノンアルメニューが用意されている</li>
                <li>飲めないことを事前に伝えれば配慮してくれる</li>
            </ul>
            <p>
                といったお店を選びましょう。
            </p>
            <p>
                「飲めないのに無理に飲ませる」お店は避けた方が無難です。
            </p>
        </section>

        {{-- Q4. タバコが苦手だけど… --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">Q4. タバコが苦手だけど…</h2>
            <p>
                <strong>A. 分煙・禁煙のお店も増えています。</strong>
            </p>
            <ul>
                <li>店内禁煙／店舗外に喫煙スペースのみ</li>
                <li>喫煙可能だけど、キャストは吸わない子が多い</li>
            </ul>
            <p>
                など、お店ごとにルールが違います。<br>
                求人や面談時に必ず確認しておきましょう。
            </p>
        </section>

        {{-- Q5. 本業や学校と両立できる？ --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">Q5. 本業や学校と両立できる？</h2>
            <p>
                <strong>A. シフトの融通が利くお店なら十分可能です。</strong>
            </p>
            <ul>
                <li>週1〜、1日3〜4時間からOK</li>
                <li>テスト期間や忙しい時期は減らせる</li>
            </ul>
            <p>
                といった条件かどうかをチェックしましょう。
            </p>
            <p>
                ただし、夜遅くまで働きすぎて本業や学校に支障が出ないよう、<br>
                <strong>自分で線引きをしておくことも大切</strong>です。
            </p>
        </section>

        {{-- Q6. 身バレが怖いです… --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">Q6. 身バレが怖いです…</h2>
            <p>
                <strong>A. きちんと対策すればリスクは減らせます。</strong>
            </p>
            <ul>
                <li>本名と完全に別の源氏名を使う</li>
                <li>仕事用アカウントとプライベートを分ける</li>
                <li>位置情報付きの投稿をしない</li>
                <li>職場や学校が分かる情報を出さない</li>
            </ul>
            <p>
                など、ルールとコツを守ることで、<br>
                身バレのリスクをかなり下げることができます。
            </p>
            <p>
                （※詳しくは「SNSの使い方と身バレ対策」のコラムで解説しています）
            </p>
        </section>

        {{-- Q7. 怖いお客さんが来たらどうすればいい？ --}}
        <section class="mb-4">
            <h2 class="column-section-title mb-2">Q7. 怖いお客さんが来たらどうすればいい？</h2>
            <p>
                <strong>A. 一人で抱えず、すぐにスタッフに相談してください。</strong>
            </p>
            <ul>
                <li>しつこく連絡先を聞いてくる</li>
                <li>誹謗中傷や威圧的な行為をする</li>
                <li>店外で会おうと強く迫ってくる</li>
            </ul>
            <p>
                などのケースは、お店側がルールに基づいて対応する領域です。
            </p>
            <p>
                「キャストを守るルール」があるお店かどうかも、<br>
                面談時にさりげなく確認しておきましょう。
            </p>
        </section>

        {{-- Q8. もし合わなかったらすぐ辞めてもいい？ --}}
        <section class="mb-2">
            <h2 class="column-section-title mb-2">Q8. もし合わなかったらすぐ辞めてもいい？</h2>
            <p>
                <strong>A. 無理に続ける必要はありません。</strong>
            </p>
            <p>
                ただし、
            </p>
            <ul>
                <li>契約で決めた期間</li>
                <li>制服や備品の返却</li>
                <li>給与の精算</li>
            </ul>
            <p>
                などのルールに沿って、<br>
                トラブルにならない形で辞めるのがお互いのためです。
            </p>
            <p>
                「合わなかったときどうすればいいか」まで<br>
                事前に説明してくれるお店は、逆に信頼できます。
            </p>
        </section>
    </article>
</div>
@endsection

