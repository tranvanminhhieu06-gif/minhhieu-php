import os
import sys
import urllib.request
import ssl
import time

# Force UTF-8 output on Windows
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8', errors='replace')

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
}

def download_image(url, destination):
    os.makedirs(os.path.dirname(destination), exist_ok=True)
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        with urllib.request.urlopen(req, context=ctx, timeout=15) as resp:
            data = resp.read()
            if len(data) > 1000:
                with open(destination, 'wb') as f:
                    f.write(data)
                print(f"[OK] {os.path.basename(destination)} ({len(data)//1024} KB)")
                return True
    except Exception as e:
        print(f"[FAIL] {os.path.basename(destination)}: {e}")
    return False

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

IMAGES = {
    # ----------------------------------------------------
    # PROJECT 1: FASHION (HieuWeb01)
    # ----------------------------------------------------
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "banners", "hero_banner_1.jpg"):
        "https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "banners", "hero_banner_2.jpg"):
        "https://images.unsplash.com/photo-1445205170230-053b83016050?w=1600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "banners", "hero_banner_3.jpg"):
        "https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1600&auto=format&fit=crop&q=85",

    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "categories", "cat_ao_thun.jpg"):
        "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "categories", "cat_ao_somi.jpg"):
        "https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "categories", "cat_ao_khoac.jpg"):
        "https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "categories", "cat_quan_jeans.jpg"):
        "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "categories", "cat_quan_kaki.jpg"):
        "https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "categories", "cat_vay_dam.jpg"):
        "https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "categories", "cat_phu_kien.jpg"):
        "https://images.unsplash.com/photo-1523779917675-b6ed3a42a561?w=800&auto=format&fit=crop&q=80",

    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_thun_streetwear.jpg"):
        "https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_polo_dệt_bo.jpg"):
        "https://images.unsplash.com/photo-1625910513413-562a0ee6db9f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_polo_det_bo.jpg"):
        "https://images.unsplash.com/photo-1625910513413-562a0ee6db9f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_thun_graphic_hieu.jpg"):
        "https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_so_mi_oxford.jpg"):
        "https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_so_mi_caro.jpg"):
        "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_so_mi_cubano.jpg"):
        "https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_khoac_bomber.jpg"):
        "https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_blazer_han_quoc.jpg"):
        "https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "ao_hoodie_ni_bong.jpg"):
        "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "quan_jean_slimfit.jpg"):
        "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "quan_jean_baggy.jpg"):
        "https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "quan_kaki_chino.jpg"):
        "https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "quan_tay_au.jpg"):
        "https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "dam_hoa_nhi.jpg"):
        "https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "chan_vay_chu_a.jpg"):
        "https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "that_lung_da.jpg"):
        "https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb01", "assets", "images", "products", "non_ket_hieumini.jpg"):
        "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=800&auto=format&fit=crop&q=80",

    # ----------------------------------------------------
    # PROJECT 2: TECH GADGETS (HieuWeb02)
    # ----------------------------------------------------
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "iphone16promax.png"):
        "https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "s24ultra.png"):
        "https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "macbookpro14.png"):
        "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "rog_g16.png"):
        "https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "ipadpro_m4.png"):
        "https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "applewatch_ultra2.png"):
        "https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "sony_wh1000xm5.png"):
        "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "marshall_stanmore3.png"):
        "https://images.unsplash.com/photo-1545454675-3531b543be5d?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "nuphy_air75.png"):
        "https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb02", "assets", "images", "anker_prime_100w.png"):
        "https://images.unsplash.com/photo-1609081219090-a6d8173087ec?w=800&auto=format&fit=crop&q=85",

    # ----------------------------------------------------
    # PROJECT 3: STATIONERY & DECOR (HieuWeb03)
    # ----------------------------------------------------
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "banners", "hero-banner.png"):
        "https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=1600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "banners", "promo-1.png"):
        "https://images.unsplash.com/photo-1585336261026-41ff3621ac7a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "banners", "promo-2.png"):
        "https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "banners", "promo-3.png"):
        "https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=800&auto=format&fit=crop&q=80",

    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p1.png"):
        "https://images.unsplash.com/photo-1585336261026-41ff3621ac7a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p2.png"):
        "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p3.png"):
        "https://images.unsplash.com/photo-1569683795645-b62e50fbf103?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p4.png"):
        "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p5.png"):
        "https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p6.png"):
        "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p7.png"):
        "https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p8.png"):
        "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p9.png"):
        "https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p10.png"):
        "https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p11.png"):
        "https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p12.png"):
        "https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p13.png"):
        "https://images.unsplash.com/photo-1513364776144-60967b0f800f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p14.png"):
        "https://images.unsplash.com/photo-1585336261026-41ff3621ac7a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p15.png"):
        "https://images.unsplash.com/photo-1579783902614-a3fb3927b675?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p16.png"):
        "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p17.png"):
        "https://images.unsplash.com/photo-1586075010923-2dd4570fb338?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p18.png"):
        "https://images.unsplash.com/photo-1507842229450-76905949c312?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p19.png"):
        "https://images.unsplash.com/photo-1586075010923-2dd4570fb338?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p20.png"):
        "https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p21.png"):
        "https://images.unsplash.com/photo-1585336261026-41ff3621ac7a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p22.png"):
        "https://images.unsplash.com/photo-1534353436294-0dbd4bdac845?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p23.png"):
        "https://images.unsplash.com/photo-1587145820266-a5951ee6f620?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p24.png"):
        "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p25.png"):
        "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p26.png"):
        "https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p27.png"):
        "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p28.png"):
        "https://images.unsplash.com/photo-1583485088034-697b5bc54ccd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p29.png"):
        "https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb03", "assets", "images", "products", "p30.png"):
        "https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop&q=80",

    # ----------------------------------------------------
    # PROJECT 4: SMART APPLIANCES (HieuWeb04)
    # ----------------------------------------------------
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "air_fryer.jpg"):
        "https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "robot_vacuum.jpg"):
        "https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "slow_juicer.jpg"):
        "https://images.unsplash.com/photo-1589734740747-0bc6c8484b77?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "air_purifier.jpg"):
        "https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "electric_kettle.jpg"):
        "https://images.unsplash.com/photo-1594213114663-d94db9e1f126?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "smart_blender.jpg"):
        "https://images.unsplash.com/photo-1570222094114-d054a817e56b?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "coffee_machine.jpg"):
        "https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "microwave_oven.jpg"):
        "https://images.unsplash.com/photo-1585659722983-3a675dabf23d?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "smart_rice_cooker.jpg"):
        "https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "garment_steamer.jpg"):
        "https://images.unsplash.com/photo-1582735689369-4fe89db7114c?w=800&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb04", "assets", "images", "products", "countertop_dishwasher.jpg"):
        "https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=800&auto=format&fit=crop&q=85",

    # ----------------------------------------------------
    # PROJECT 5: LUXURY FITNESS (HieuWeb05)
    # ----------------------------------------------------
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "hero-gym.jpg"):
        "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "hero_ai.jpg"):
        "https://images.unsplash.com/photo-1574680096145-d05b474e2155?w=1600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "sauna-spa.jpg"):
        "https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=1200&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "ceo_avatar.jpg"):
        "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "member_avatar.jpg"):
        "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=85",

    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "cat_membership.jpg"):
        "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "cat_equipment.jpg"):
        "https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "cat_supplements.jpg"):
        "https://images.unsplash.com/photo-1584017911766-d451b3d0e843?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "cat_apparel.jpg"):
        "https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "cat_pt.jpg"):
        "https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&auto=format&fit=crop&q=80",

    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "trainers", "trainer_1.jpg"):
        "https://images.unsplash.com/photo-1567013127542-490d757e51fc?w=600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "trainers", "trainer_2.jpg"):
        "https://images.unsplash.com/photo-1594381898411-846e7d193883?w=600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "trainers", "trainer_3.jpg"):
        "https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=600&auto=format&fit=crop&q=85",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "trainers", "trainer_4.jpg"):
        "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=600&auto=format&fit=crop&q=85",

    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "01_membership_diamond.jpg"):
        "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "02_membership_gold.jpg"):
        "https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "03_membership_platinum.jpg"):
        "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "04_day_pass.jpg"):
        "https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "05_corporate_club.jpg"):
        "https://images.unsplash.com/photo-1574680096145-d05b474e2155?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "06_commercial_treadmill.jpg"):
        "https://images.unsplash.com/photo-1576678927484-cc907957088c?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "07_power_rack.jpg"):
        "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "08_smith_machine.jpg"):
        "https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "09_cable_crossover.jpg"):
        "https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "10_olympic_barbell_set.jpg"):
        "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "11_smart_dumbbells.jpg"):
        "https://images.unsplash.com/photo-1586401100295-7a8096fd231a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "12_water_rower.jpg"):
        "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "13_kettlebell_set.jpg"):
        "https://images.unsplash.com/photo-1586401100295-7a8096fd231a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "14_whey_isolate.jpg"):
        "https://images.unsplash.com/photo-1584017911766-d451b3d0e843?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "15_creatine_creapure.jpg"):
        "https://images.unsplash.com/photo-1584017911766-d451b3d0e843?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "16_pre_workout.jpg"):
        "https://images.unsplash.com/photo-1579758629938-03607ccdbaba?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "17_bcaa_eaa.jpg"):
        "https://images.unsplash.com/photo-1584017911766-d451b3d0e843?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "18_mass_gainer.jpg"):
        "https://images.unsplash.com/photo-1584017911766-d451b3d0e843?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "19_omega3_fishoil.jpg"):
        "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "20_multivitamin.jpg"):
        "https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "21_protein_bar.jpg"):
        "https://images.unsplash.com/photo-1622484216298-5c4e70e28f31?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "22_lever_belt.jpg"):
        "https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "23_knee_sleeves.jpg"):
        "https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "24_figure8_straps.jpg"):
        "https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "25_steel_shaker.jpg"):
        "https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "26_dryfit_tee.jpg"):
        "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "27_master_trainer.jpg"):
        "https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "28_sports_therapy.jpg"):
        "https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "29_inbody_scan.jpg"):
        "https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&auto=format&fit=crop&q=80",
    os.path.join(ROOT, "projects", "HieuWeb05", "assets", "images", "products", "30_yoga_meditation.jpg"):
        "https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=800&auto=format&fit=crop&q=80",
}

print(f"Starting download of {len(IMAGES)} images...")
success = 0
for path, url in IMAGES.items():
    if download_image(url, path):
        success += 1
    time.sleep(0.05)

print(f"\nAll Done! Downloaded {success}/{len(IMAGES)} images.")
