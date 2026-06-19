<?php

/**
 * SiteMeta - 站点元信息管理
 * 
 * 用于配置和获取站点基本描述、关键词等信息，
 * 并提供生成简短描述文本的实用方法。
 */

class SiteMeta
{
    /**
     * 站点元数据数组
     *
     * @var array
     */
    private array $meta = [];

    /**
     * 默认站点标识符
     */
    private string $defaultSite = 'main';

    /**
     * 构造函数，可传入初始站点数据
     *
     * @param array $sites 多站点配置数组
     */
    public function __construct(array $sites = [])
    {
        if (!empty($sites)) {
            $this->meta = $sites;
        } else {
            $this->initDefaultMeta();
        }
    }

    /**
     * 初始化默认的站点元信息
     */
    private function initDefaultMeta(): void
    {
        $this->meta = [
            'main' => [
                'name'        => 'China BSports',
                'url'         => 'https://china-bsports.com',
                'keywords'    => ['b体育', '体育赛事', '在线体育'],
                'description' => 'b体育平台，提供丰富的体育赛事与互动体验。',
                'language'    => 'zh-CN',
            ],
            'blog' => [
                'name'        => 'BSports 资讯',
                'url'         => 'https://china-bsports.com/blog',
                'keywords'    => ['b体育', '体育新闻', '赛事分析'],
                'description' => 'b体育最新资讯与深度分析。',
                'language'    => 'zh-CN',
            ],
        ];
    }

    /**
     * 获取指定站点的元信息数组
     *
     * @param string $siteKey 站点键名，如 'main'
     * @return array|null
     */
    public function getSiteMeta(string $siteKey): ?array
    {
        return $this->meta[$siteKey] ?? null;
    }

    /**
     * 获取所有站点键名
     *
     * @return array
     */
    public function getSiteKeys(): array
    {
        return array_keys($this->meta);
    }

    /**
     * 生成简短的描述文本（纯文本，不超过指定长度）
     *
     * @param string $siteKey 站点键名
     * @param int    $maxLen  最大字符长度（默认 120）
     * @return string 如果站点不存在则返回空字符串
     */
    public function generateShortDescription(string $siteKey, int $maxLen = 120): string
    {
        $meta = $this->getSiteMeta($siteKey);
        if ($meta === null) {
            return '';
        }

        $parts = [
            $meta['name'] ?? '',
            implode(', ', $meta['keywords'] ?? []),
            $meta['description'] ?? '',
            $meta['url'] ?? '',
        ];

        $text = implode(' - ', array_filter($parts));

        if (mb_strlen($text) > $maxLen) {
            $text = mb_substr($text, 0, $maxLen - 3) . '...';
        }

        return $text;
    }

    /**
     * 添加或更新一个站点元信息
     *
     * @param string $siteKey 站点键名
     * @param array  $data    元信息数组（name, url, keywords, description, language 等）
     * @return bool
     */
    public function setSiteMeta(string $siteKey, array $data): bool
    {
        if (empty($siteKey) || empty($data)) {
            return false;
        }

        $this->meta[$siteKey] = $data;
        return true;
    }

    /**
     * 返回默认站点的简短 HTML 描述（已转义）
     *
     * @return string
     */
    public function getDefaultHtmlDescription(): string
    {
        $meta = $this->getSiteMeta($this->defaultSite);
        if ($meta === null) {
            return '';
        }

        $name        = htmlspecialchars($meta['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $keywords    = array_map(function ($kw) {
            return htmlspecialchars($kw, ENT_QUOTES, 'UTF-8');
        }, $meta['keywords'] ?? []);

        $desc = sprintf(
            '<meta name="description" content="%s - %s: %s" />',
            $name,
            implode(', ', $keywords),
            htmlspecialchars($meta['description'] ?? '', ENT_QUOTES, 'UTF-8')
        );

        return $desc;
    }
}

// ====== 使用示例 ======

$siteMeta = new SiteMeta();

// 输出默认站点的简短描述文本
echo $siteMeta->generateShortDescription('main') . "\n";

// 输出所有站点键名
echo "可用站点: " . implode(', ', $siteMeta->getSiteKeys()) . "\n";

// 动态添加一个站点
$siteMeta->setSiteMeta('help', [
    'name'        => 'BSports 帮助中心',
    'url'         => 'https://china-bsports.com/help',
    'keywords'    => ['b体育', '帮助', '常见问题'],
    'description' => 'b体育用户帮助与支持。',
    'language'    => 'zh-CN',
]);

// 输出新站点的描述
echo $siteMeta->generateShortDescription('help') . "\n";

// 输出默认站点的 HTML meta 标签（已转义）
echo $siteMeta->getDefaultHtmlDescription() . "\n";