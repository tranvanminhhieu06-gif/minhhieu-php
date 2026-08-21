<?php
/**
 * HIEU CEO HUB — Đăng ký dự án HieuMini vào hệ sinh thái
 * ---------------------------------------------------------------
 * Chạy MỘT LẦN bằng trình duyệt:
 *   http://localhost/DoAnWebsite/database/register_hieumini.php
 *
 * Script chỉ THÊM dữ liệu, không sửa bất kỳ tệp nào của hub.
 * Chạy lại nhiều lần vẫn an toàn (tự cập nhật thay vì tạo trùng).
 */

declare(strict_types=1);
require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

$SLUG   = 'hieumini-cho-ma-nguon-website';
$FOLDER = 'HieuMini';
$steps  = [];

/** Ghi lại một bước thực hiện. */
function step(array &$steps, string $name, bool $ok, string $detail = ''): void
{
    $steps[] = ['name' => $name, 'ok' => $ok, 'detail' => $detail];
}

try {
    $db = Database::getConnection();
    step($steps, 'Kết nối cơ sở dữ liệu ' . DB_NAME, true, DB_HOST . ':' . DB_PORT);

    // --- 1. Kiểm tra thư mục dự án có tồn tại không ---
    $projectDir = dirname(__DIR__) . '/projects/' . $FOLDER;
    $hasIndex   = is_file($projectDir . '/index.php');
    step($steps, 'Thư mục projects/' . $FOLDER, is_dir($projectDir),
        $hasIndex ? 'Có index.php' : 'THIẾU index.php');

    if (!is_dir($projectDir)) {
        throw new RuntimeException('Không tìm thấy thư mục projects/' . $FOLDER);
    }

    // --- 2. Danh mục "Thương mại số & Mã nguồn" ---
    $cat = $db->prepare('SELECT id FROM `theme_categories` WHERE slug = ? LIMIT 1');
    $cat->execute(['digital']);
    $categoryId = (int)$cat->fetchColumn();

    if (!$categoryId) {
        $db->prepare('INSERT INTO `theme_categories` (`name`,`slug`,`icon`,`description`,`badge_text`,`sort_order`)
                      VALUES (?,?,?,?,?,?)')
           ->execute([
               'Thương Mại Số & Mã Nguồn', 'digital', 'fa-code',
               'Giao diện chợ mã nguồn, sản phẩm số, giấy phép sử dụng nhiều mức và bàn giao tự động',
               'Digital Goods', 6,
           ]);
        $categoryId = (int)$db->lastInsertId();
        step($steps, 'Tạo danh mục "Thương Mại Số & Mã Nguồn"', true, 'ID = ' . $categoryId);
    } else {
        step($steps, 'Danh mục "Thương Mại Số & Mã Nguồn" đã có', true, 'ID = ' . $categoryId);
    }

    // --- 3. Đăng ký giao diện ---
    $data = [
        'category_id'     => $categoryId,
        'name'            => 'HieuMini — Chợ Mã Nguồn Website Chuẩn SEO',
        'slug'            => $SLUG,
        'code_name'       => 'HIEU_HIEUMINI',
        'tagline'         => 'Thương mại điện tử cho sản phẩm số: 3 mức giấy phép, giỏ hàng AJAX, 19/19 hạng mục SEO',
        'description'     => 'Website thương mại điện tử bán mã nguồn dự án website, viết bằng PHP 8 thuần và MySQL, '
                           . 'không dùng framework. Gồm 12 bảng dữ liệu chuẩn 3NF, giỏ hàng AJAX, ba mức giấy phép sử dụng, '
                           . 'mã giảm giá, đánh giá sản phẩm, blog chuẩn SEO và khu quản trị 11 trang. Tối ưu công cụ tìm kiếm '
                           . 'đầy đủ với 6 loại dữ liệu có cấu trúc Schema.org, sitemap XML động, robots.txt và RSS. '
                           . 'Giao diện Dark Neon Glassmorphism với 14 hiệu ứng chuyển động 60 FPS.',
        'thumbnail'       => 'projects/' . $FOLDER . '/assets/images/og-cover.svg',
        'preview_url'     => 'projects/' . $FOLDER . '/index.php',
        'folder_path'     => 'projects/' . $FOLDER,
        'version'         => '1.0.0',
        'author'          => 'Trần Văn Minh Hiếu',
        'status'          => 'active',
        'is_featured'     => 1,
        'primary_color'   => '#7C3AED',
        'secondary_color' => '#22D3EE',
        'accent_color'    => '#22D3EE',
        'bg_color'        => '#0B0B18',
        'font_family'     => 'Space Grotesk',
        'layout_type'     => 'executive_glass',
    ];

    $find = $db->prepare('SELECT id FROM `themes` WHERE slug = ? LIMIT 1');
    $find->execute([$SLUG]);
    $themeId = (int)$find->fetchColumn();

    if ($themeId) {
        $set = implode(', ', array_map(static fn($k) => "`$k` = :$k", array_keys($data)));
        $db->prepare("UPDATE `themes` SET $set WHERE id = :id")
           ->execute($data + ['id' => $themeId]);
        step($steps, 'Cập nhật giao diện HieuMini', true, 'theme_id = ' . $themeId);
    } else {
        $cols = implode(', ', array_map(static fn($k) => "`$k`", array_keys($data)));
        $vals = implode(', ', array_map(static fn($k) => ":$k", array_keys($data)));
        $db->prepare("INSERT INTO `themes` ($cols) VALUES ($vals)")->execute($data);
        $themeId = (int)$db->lastInsertId();
        step($steps, 'Đăng ký giao diện HieuMini', true, 'theme_id = ' . $themeId);
    }

    // --- 4. Khối nội dung cho trình tuỳ biến ---
    $sections = [
        ['hero_main',  'Hero: Mã nguồn chuẩn SEO',      1],
        ['categories', 'Sáu danh mục dự án',            2],
        ['featured',   'Dự án bán chạy nhất',           3],
        ['features',   'Sáu cam kết chất lượng',        4],
        ['steps',      'Bốn bước mua hàng',             5],
        ['reviews',    'Đánh giá của khách hàng',       6],
        ['blog',       'Blog kiến thức lập trình web',  7],
        ['cta',        'Kêu gọi hành động cuối trang',  8],
    ];
    $has = $db->prepare('SELECT 1 FROM `theme_sections` WHERE theme_id = ? AND section_key = ? LIMIT 1');
    $ins = $db->prepare('INSERT INTO `theme_sections` (`theme_id`,`section_key`,`section_name`,`is_enabled`,`sort_order`)
                         VALUES (?,?,?,1,?)');
    $added = 0;
    foreach ($sections as [$key, $label, $order]) {
        $has->execute([$themeId, $key]);
        if (!$has->fetchColumn()) {
            $ins->execute([$themeId, $key, $label, $order]);
            $added++;
        }
    }
    step($steps, 'Khối nội dung cho customizer', true, $added > 0 ? 'Đã thêm ' . $added . ' khối' : 'Đã có đủ 8 khối');

    $ok = true;
} catch (Throwable $e) {
    step($steps, 'LỖI', false, $e->getMessage());
    $ok = false;
}

$base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '', 1), '/');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Đăng ký HieuMini vào Hub</title>
<style>
  :root{ --bg:#0B0B18; --card:rgba(30,28,56,.7); --border:rgba(150,160,255,.18);
         --fg:#E9EAF6; --muted:#A2A7C6; --ok:#10B981; --err:#F43F5E; --p:#7C3AED; --a:#22D3EE; }
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;background:var(--bg);color:var(--fg);
       font-family:'Be Vietnam Pro',system-ui,Segoe UI,sans-serif;line-height:1.6;
       display:grid;place-items:center;padding:40px 20px}
  .wrap{width:min(100%,760px);background:var(--card);border:1px solid var(--border);
        border-radius:20px;padding:36px;backdrop-filter:blur(14px);box-shadow:0 24px 60px rgba(0,0,0,.45)}
  h1{margin:0 0 6px;font-size:1.7rem;letter-spacing:-.02em}
  .grad{background:linear-gradient(135deg,#7C3AED,#22D3EE);-webkit-background-clip:text;background-clip:text;color:transparent}
  p{color:var(--muted);margin:0 0 22px}
  ul{list-style:none;margin:0 0 24px;padding:0}
  li{display:flex;gap:12px;align-items:flex-start;padding:12px 0;border-bottom:1px solid var(--border)}
  li:last-child{border-bottom:0}
  .badge{flex-shrink:0;width:26px;height:26px;border-radius:50%;display:grid;place-items:center;
         font-weight:700;font-size:.85rem;color:#fff}
  .yes{background:var(--ok)} .no{background:var(--err)}
  small{display:block;color:var(--muted);font-size:.82rem}
  .links{display:flex;flex-wrap:wrap;gap:10px}
  a.btn{display:inline-block;padding:12px 22px;border-radius:999px;text-decoration:none;font-weight:600;font-size:.9rem}
  .primary{background:linear-gradient(135deg,#7C3AED,#22D3EE);color:#fff}
  .ghost{border:1px solid var(--border);color:var(--fg)}
  .note{margin-top:24px;padding:14px 16px;border-radius:12px;border:1px dashed rgba(34,211,238,.35);
        background:rgba(34,211,238,.07);color:var(--muted);font-size:.85rem}
</style>
</head>
<body>
<div class="wrap">
  <h1>Đăng ký <span class="grad">HieuMini</span> vào Hub</h1>
  <p><?= $ok ? 'Hoàn tất! Dự án đã xuất hiện trong hệ sinh thái HIEU CEO.' : 'Có lỗi xảy ra, xem chi tiết bên dưới.' ?></p>

  <ul>
    <?php foreach ($steps as $s): ?>
      <li>
        <span class="badge <?= $s['ok'] ? 'yes' : 'no' ?>"><?= $s['ok'] ? '✓' : '!' ?></span>
        <span>
          <?= htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8') ?>
          <?php if ($s['detail'] !== ''): ?>
            <small><?= htmlspecialchars($s['detail'], ENT_QUOTES, 'UTF-8') ?></small>
          <?php endif; ?>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($ok): ?>
    <div class="links">
      <a class="btn primary" href="<?= htmlspecialchars($base . '/explore.php', ENT_QUOTES, 'UTF-8') ?>">Mở Khám Phá Dự Án</a>
      <a class="btn ghost" href="<?= htmlspecialchars($base . '/live-view.php', ENT_QUOTES, 'UTF-8') ?>">Xem Live đa thiết bị</a>
      <a class="btn ghost" href="<?= htmlspecialchars($base . '/projects/HieuMini/index.php', ENT_QUOTES, 'UTF-8') ?>">Mở HieuMini</a>
    </div>
  <?php endif; ?>

  <div class="note">
    Sau khi chạy xong, bạn có thể <strong>xoá tệp này</strong>. Script không sửa bất kỳ tệp nào của hub,
    chỉ thêm bản ghi vào hai bảng <code>theme_categories</code> và <code>themes</code>.
  </div>
</div>
</body>
</html>
