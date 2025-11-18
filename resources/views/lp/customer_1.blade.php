@extends('layouts.app')

@section('title', '【店舗様向け】集客・求人ならカフェコレ | cafecolle.jp')
@section('description', 'メイドカフェに特化した集客・求人プラットフォーム「カフェコレ」。ターゲット層へ確実にアプローチ！貴店の魅力を最大限に引き出し、理想のキャストとお客様をマッチングします。')

@section('content')
<!-- ヒーローセクション -->
<section class="cc-hero-section">
    <div class="cc-container position-relative" style="z-index: 1;">
        <div class="cc-card text-center">
            <p class="mb-2" style="color: var(--cc-color-accent); font-weight: 600;">売上向上、人手不足解消へ。</p>
            <h1 class="h2 mb-3 fw-bold">メイドカフェに特化した集客・求人プラットフォーム <span style="color: var(--cc-color-accent);">カフェコレ</span></h1>
            <p class="mb-4">ターゲット層へ確実にアプローチ！貴店の魅力を最大限に引き出し、理想のキャストとお客様をマッチングします。</p>
            
            <a href="#contact" class="btn btn-primary btn-lg mb-2">
                無料資料請求・お問い合わせはこちら
            </a>
            <p class="small text-muted mb-0">※掲載に関するご相談も承ります</p>
        </div>
    </div>
</section>

<!-- 課題セクション -->
<section class="py-5">
    <div class="cc-container">
        <div class="cc-card">
            <h2 class="h4 mb-4 text-center">貴店の集客・採用、こんな課題を抱えていませんか？</h2>
            <ul class="list-unstyled">
                <li class="mb-3 d-flex align-items-start">
                    <i class="fas fa-frown me-3 mt-1" style="color: var(--cc-color-accent); font-size: 20px;"></i>
                    <span>優良なキャストがなかなか集まらない</span>
                </li>
                <li class="mb-3 d-flex align-items-start">
                    <i class="fas fa-frown me-3 mt-1" style="color: var(--cc-color-accent); font-size: 20px;"></i>
                    <span>集客サイトへの掲載コストが高い割に効果が薄い</span>
                </li>
                <li class="mb-3 d-flex align-items-start">
                    <i class="fas fa-frown me-3 mt-1" style="color: var(--cc-color-accent); font-size: 20px;"></i>
                    <span>ターゲット層（メイドカフェ好き）に情報が届いていない</span>
                </li>
                <li class="mb-3 d-flex align-items-start">
                    <i class="fas fa-frown me-3 mt-1" style="color: var(--cc-color-accent); font-size: 20px;"></i>
                    <span>お店の魅力が伝わりにくく、定着率が低い</span>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- 選ばれる理由セクション -->
<section id="service" class="py-5" style="background-color: var(--cc-color-bg);">
    <div class="cc-container">
        <h2 class="h4 mb-4 text-center">メイドカフェ特化型「カフェコレ」が選ばれる理由</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="cc-card h-100 text-center">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16" style="color: var(--cc-color-accent);">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                    </div>
                    <h3 class="h5 mb-3">【特化型集客力】熱量の高い「メイドカフェファン」に直結！</h3>
                    <p class="small">一般の求人/グルメサイトではリーチできないコアなファン層への訴求が可能。高い費用対効果が見込めます。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="cc-card h-100 text-center">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16" style="color: var(--cc-color-accent);">
                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                        </svg>
                    </div>
                    <h3 class="h5 mb-3">【質の高い採用】モチベーションの高いキャスト候補者を確実に確保！</h3>
                    <p class="small">メイドカフェで働きたいと明確に考えている求職者が集まるため、貴店の採用コスト削減と定着率向上に貢献します。</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="cc-card h-100 text-center">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16" style="color: var(--cc-color-accent);">
                            <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z"/>
                        </svg>
                    </div>
                    <h3 class="h5 mb-3">【コストパフォーマンス】費用対効果の高い料金プランをご提案！</h3>
                    <p class="small">貴店の規模や課題に合わせた柔軟な掲載プランをご用意。無駄なコストをかけずに最大の効果を目指します。</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- メリットセクション -->
<section class="py-5">
    <div class="cc-container">
        <div class="cc-card">
            <h2 class="h4 mb-4 text-center">カフェコレで実現する3つのメリット</h2>
            <dl class="row g-4">
                <dt class="col-md-3 fw-bold" style="color: var(--cc-color-accent);">メリット 1：集客</dt>
                <dd class="col-md-9">店舗詳細・イベント情報を魅力的に発信。豊富な写真、詳細なメニュー、イベントカレンダー機能など、来店意欲を高める情報掲載が可能。</dd>
                
                <dt class="col-md-3 fw-bold" style="color: var(--cc-color-accent);">メリット 2：求人</dt>
                <dd class="col-md-9">高待遇求人としてキャストにダイレクトアピール。時給、シフト、制服の写真など、求職者が知りたい情報を網羅した専用求人ページを作成。</dd>

                <dt class="col-md-3 fw-bold" style="color: var(--cc-color-accent);">メリット 3：ブランディング</dt>
                <dd class="col-md-9">貴店の個性を引き出す特集記事・SNS連携。お店のコンセプトやオーナーの想いなどを伝える記事制作サポートや、SNSとの連携強化。</dd>
            </dl>
        </div>
    </div>
</section>

<!-- 導入事例セクション -->
<section id="case" class="py-5" style="background-color: var(--cc-color-bg);">
    <div class="cc-container">
        <h2 class="h4 mb-4 text-center">導入店舗様の声 / 導入事例</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="cc-card h-100">
                    <blockquote class="mb-0">
                        <p class="mb-3">「掲載後、応募数が2倍に増加しました。質の高いキャストからの応募が増え、大変満足しています。」</p>
                        <footer class="blockquote-footer">
                            <span style="color: var(--cc-color-accent);">⭐</span> 事例 1：○○店様 (オーナー)
                        </footer>
                    </blockquote>
                </div>
            </div>
            <div class="col-md-6">
                <div class="cc-card h-100">
                    <blockquote class="mb-0">
                        <p class="mb-3">「イベント時の集客が格段に良くなりました。専門サイトならではの集客力を実感しています。」</p>
                        <footer class="blockquote-footer">
                            <span style="color: var(--cc-color-accent);">⭐</span> 事例 2：△△店様 (店長)
                        </footer>
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- フローセクション -->
<section class="py-5">
    <div class="cc-container">
        <div class="cc-card">
            <h2 class="h4 mb-4 text-center">掲載開始までの簡単ステップ</h2>
            <ol class="list-unstyled">
                <li class="mb-4 d-flex align-items-start">
                    <span class="badge rounded-pill me-3" style="background-color: var(--cc-color-accent); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px;">1</span>
                    <div>
                        <strong>【お問い合わせ】</strong>：無料資料請求、または掲載相談フォームよりご連絡。
                    </div>
                </li>
                <li class="mb-4 d-flex align-items-start">
                    <span class="badge rounded-pill me-3" style="background-color: var(--cc-color-accent); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px;">2</span>
                    <div>
                        <strong>【ヒアリング・プラン提案】</strong>：貴店の課題、求めている人材をヒアリングし、最適なプランをご提案。
                    </div>
                </li>
                <li class="mb-4 d-flex align-items-start">
                    <span class="badge rounded-pill me-3" style="background-color: var(--cc-color-accent); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 18px;">3</span>
                    <div>
                        <strong>【情報作成・掲載開始】</strong>：原稿作成サポートを経て、掲載スタート！
                    </div>
                </li>
            </ol>
        </div>
    </div>
</section>

<!-- CTAセクション -->
<section id="contact" class="py-5 mb-4" style="background-color: var(--cc-color-main);">
    <div class="cc-container text-center">
        <h2 class="h4 mb-3">貴店の魅力を、まだ見ぬお客様とキャストへ</h2>
        <p class="mb-4">まずはカフェコレのサービス内容を詳しくご確認ください。専門スタッフが親身になってサポートいたします。</p>

        <a href="/contact/store" class="btn btn-primary btn-lg">
            資料請求・掲載について相談する
        </a>
    </div>
</section>
@endsection
