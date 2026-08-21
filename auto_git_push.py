#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
=====================================================================
HIEU CEO - REALTIME AUTO GIT COMMIT & PUSH (PYTHON WATCHER)
=====================================================================
Không cần cài thêm thư viện (chỉ dùng Python standard library).
Tự động quét thay đổi tệp, gom debounce và push lên GitHub.
=====================================================================
"""

import os
import sys
import time
import subprocess
from datetime import datetime

# Cấu hình
DEBOUNCE_SECONDS = 7
REMOTE = "origin"
BRANCH = "main"
IGNORE_DIRS = {'.git', '.vscode', '.idea', 'node_modules', 'vendor', '__pycache__'}
IGNORE_EXTS = {'.tmp', '.temp', '.swp', '.log', '.crdownload'}

REPO_DIR = os.path.dirname(os.path.abspath(__file__))

def get_files_snapshot(root_dir):
    """Lấy danh sách tệp cùng mtime để so sánh thay đổi"""
    snapshot = {}
    for dirpath, dirnames, filenames in os.walk(root_dir):
        # Bỏ qua các thư mục không cần theo dõi
        dirnames[:] = [d for d in dirnames if d not in IGNORE_DIRS]
        for f in filenames:
            ext = os.path.splitext(f)[1].lower()
            if ext in IGNORE_EXTS or f.startswith('~') or f.endswith('.tmp'):
                continue
            full_path = os.path.join(dirpath, f)
            try:
                snapshot[full_path] = os.path.getmtime(full_path)
            except OSError:
                pass
    return snapshot

def run_git_command(args):
    try:
        res = subprocess.run(['git'] + args, cwd=REPO_DIR, capture_output=True, text=True, encoding='utf-8', errors='ignore')
        return res.returncode == 0, res.stdout.strip(), res.stderr.strip()
    except Exception as e:
        return False, "", str(e)

def perform_sync(reason=""):
    ok, status_out, _ = run_git_command(['status', '--porcelain'])
    if not status_out:
        return
    
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    lines = status_out.splitlines()
    print(f"\n📦 [{timestamp}] Phát hiện {len(lines)} tệp thay đổi. Đang tiến hành đồng bộ Git...")
    
    # 1. git add
    run_git_command(['add', '-A'])
    
    # 2. git commit
    msg = f"Auto sync [{timestamp}] - Cập nhật {len(lines)} tệp"
    if reason:
        msg += f" ({reason})"
    
    ok_commit, commit_out, _ = run_git_command(['commit', '-m', msg])
    print(f"📝 {msg}")
    
    # 3. git push
    print(f"🚀 Đang đẩy lên GitHub ({REMOTE}/{BRANCH})...")
    ok_push, push_out, push_err = run_git_command(['push', REMOTE, BRANCH])
    
    if ok_push:
        print(f"✅ [{timestamp}] ĐỒNG BỘ THÀNH CÔNG LÊN GITHUB!\n")
    else:
        print(f"⚠️ Lỗi khi đẩy lên Git: {push_err or push_out}\n")

def main():
    os.system('cls' if os.name == 'nt' else 'clear')
    print("=" * 65)
    print(" 🚀 AUTO GIT WATCHER (PYTHON) - HIEU CEO DEV TOOLS")
    print(f" 📂 Thư mục: {REPO_DIR}")
    print(f" 🌿 Nhánh  : {REMOTE} / {BRANCH}")
    print(f" ⏱️  Gom file: {DEBOUNCE_SECONDS}s sau lần sửa cuối")
    print(" 🛑 Nhấn Ctrl + C để dừng lại.")
    print("=" * 65)
    
    # Kiểm tra và sync các thay đổi có sẵn
    perform_sync("Đồng bộ ban đầu")
    
    last_snapshot = get_files_snapshot(REPO_DIR)
    pending_sync = False
    last_change_time = 0
    changed_desc = ""
    
    print("\n🟢 [ĐANG HOẠT ĐỘNG] Trình theo dõi đã sẵn sàng. Hãy code và lưu file tự nhiên!\n")
    
    try:
        while True:
            time.sleep(1.0)
            current_snapshot = get_files_snapshot(REPO_DIR)
            
            # So sánh snapshot
            added_modified = [p for p in current_snapshot if p not in last_snapshot or current_snapshot[p] != last_snapshot[p]]
            deleted = [p for p in last_snapshot if p not in current_snapshot]
            
            if added_modified or deleted:
                sample_file = added_modified[0] if added_modified else deleted[0]
                rel_sample = os.path.relpath(sample_file, REPO_DIR)
                action_str = "Sửa/Thêm" if added_modified else "Xóa"
                
                cur_time = datetime.now().strftime("%H:%M:%S")
                print(f"🔔 [{cur_time}] Thay đổi: {rel_sample} [{action_str}]")
                
                last_snapshot = current_snapshot
                pending_sync = True
                last_change_time = time.time()
                changed_desc = f"{rel_sample}"
            
            if pending_sync and (time.time() - last_change_time >= DEBOUNCE_SECONDS):
                pending_sync = False
                perform_sync(changed_desc)
                last_snapshot = get_files_snapshot(REPO_DIR)
                
    except KeyboardInterrupt:
        print("\n🛑 Đã tắt trình tự động đẩy Git.")

if __name__ == "__main__":
    main()
