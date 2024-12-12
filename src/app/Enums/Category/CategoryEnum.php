<?php

declare(strict_types=1);

namespace App\Enums\Category;

enum CategoryEnum: int
{
    /**
     * 総合
     */
    case ALL = 0;

    /**
     * テレビ・映像制作
     */
    case VIDEO = 1;

    /**
     * 音楽
     */
    case MUSIC = 10;

    /**
     * スポーツ
     */
    case SPORTS = 17;

    /**
     * ゲーム
     */
    case GAME = 20;

    /**
     * エンターテインメント
     */
    case ENTERTAINMENT = 24;

    /**
     * ニュース
     */
    case NEWS = 25;

    /**
     * HOW TO
     */
    case HOW_TO = 26;

    /**
     * 値を配列として返す
     *
     * @return array
     */
    public static function toArray(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * 名前（定数名）を返す
     *
     * @return string
     */
    public function constantName(): string
    {
        return $this->name;
    }

    /**
     * 説明を返す
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::ALL => '総合',
            self::VIDEO => 'テレビ・映像制作',
            self::MUSIC => '音楽',
            self::SPORTS => 'スポーツ',
            self::GAME => 'ゲーム',
            self::ENTERTAINMENT => 'エンターテインメント',
            self::NEWS => 'ニュース',
            self::HOW_TO => 'HOW TO',
        };
    }
}
