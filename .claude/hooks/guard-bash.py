#!/usr/bin/env python3
"""PreToolUse(Bash) ガード。AI が打とうとしたコマンドを実行前に読み、危険なものを止める。

- exit 2 ＝ 止める（理由が AI に返る。人が自分の端末で実行する判断になる）
- exit 0 ＝ 通す
- 判定できないとき（JSON が読めない等）は通す。止める側の理由は stderr に出す

止める対象は「取り消せない操作」と「秘密ファイルの読み出し」だけ。
通常の開発操作（テスト・マイグレーション・コミット・通常の push）は止めない。
push は settings.json の permissions.ask で毎回聞く形にしてある（このスクリプトの役目ではない）。
"""
import json
import re
import sys

RULES = [
    # (正規表現, 止める理由)
    (r"\bgit\s+push\b[^|;&]*(\s-f\b|\s--force\b|\s--force-with-lease\b)",
     "強制 push は履歴を上書きする。人が自分で実行する"),
    (r"\bgit\s+reset\s+--hard\b",
     "reset --hard は作業中の変更を捨てる。人が自分で実行する"),
    (r"\bgit\s+branch\b[^|;&]*\s-D\b",
     "branch -D はマージ確認を飛ばして消す。-d を使う"),
    (r"\bgit\s+clean\b[^|;&]*\s-[a-zA-Z]*f",
     "git clean -f は未追跡ファイルを消す。人が自分で実行する"),
    (r"\brm\s+(-[a-zA-Z]*[rR][a-zA-Z]*\s|-[a-zA-Z]*[rR]$|-r\s+-f\b|-f\s+-r\b)",
     "rm -r は取り消せない。人が自分で実行する"),
    (r"\b(migrate:fresh|migrate:refresh|migrate:reset|db:wipe)\b",
     "DB を作り直すコマンド。接続先と Docker の状態を人が確認してから実行する（CLAUDE.core §4）"),
    (r"\bdocker\s+compose\b[^|;&]*\bdown\b[^|;&]*(\s-v\b|\s--volumes\b)",
     "docker compose down -v は DB のデータごと消す。人が自分で実行する"),
    (r"\b(cat|head|tail|less|more|sed|grep|egrep|awk|strings|bat|od|xxd|base64|source|tee|vim|nano)\b"
     r"[^|;&]*\.env(?!\.example\b|\.testing\.example\b)(\.[\w-]+)?(?=[\s'\"\)]|$)",
     "秘密ファイル（.env 系）の中身は読まない。設定値は .env.example / .env.testing.example を見る"),
]


def main() -> int:
    try:
        data = json.load(sys.stdin)
    except Exception:
        return 0
    cmd = ""
    if isinstance(data, dict):
        cmd = (data.get("tool_input") or {}).get("command") or ""
    if not isinstance(cmd, str) or not cmd:
        return 0
    for pattern, reason in RULES:
        if re.search(pattern, cmd):
            print(f"[guard-bash] 止めました: {reason}\n  コマンド: {cmd}", file=sys.stderr)
            return 2
    return 0


if __name__ == "__main__":
    sys.exit(main())
