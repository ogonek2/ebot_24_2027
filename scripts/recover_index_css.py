#!/usr/bin/env python3
"""Reconstruct index.css from base + agent transcript patches."""
import json
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
BASE = ROOT / "src" / "index.css.base"
CART = ROOT / "src" / "index.css"
OUT = ROOT / "src" / "index.css"
TRANSCRIPT = Path(
    r"C:\Users\ytlem\.cursor\projects\d-OpenServer-domains-localhost-enot-24-2027"
    r"\agent-transcripts\9005395f-066c-4df6-8160-e648d741e0a1"
    r"\9005395f-066c-4df6-8160-e648d741e0a1.jsonl"
)

TARGET_SUFFIX = "frontend/src/index.css"


def norm_path(p: str) -> str:
    return p.replace("\\", "/").lower()


def extract_ops(transcript: Path) -> list:
    ops = []
    with transcript.open(encoding="utf-8") as f:
        for line in f:
            try:
                obj = json.loads(line)
            except json.JSONDecodeError:
                continue
            msg = obj.get("message") or {}
            content = msg.get("content")
            if not isinstance(content, list):
                continue
            for part in content:
                if not isinstance(part, dict) or part.get("type") != "tool_use":
                    continue
                name = part.get("name")
                inp = part.get("input")
                if not isinstance(inp, dict):
                    continue
                path = inp.get("path", "")
                if not norm_path(path).endswith(TARGET_SUFFIX):
                    continue
                if name == "Write":
                    ops.append(("write", inp.get("contents", "")))
                elif name == "StrReplace":
                    ops.append(("replace", inp.get("old_string", ""), inp.get("new_string", "")))
    return ops


def main() -> int:
    content = BASE.read_text(encoding="utf-8")
    ops = extract_ops(TRANSCRIPT)
    print(f"Found {len(ops)} operations on index.css")

    applied = 0
    failed = []
    for i, op in enumerate(ops):
        if op[0] == "write":
            content = op[1]
            applied += 1
        else:
            old, new = op[1], op[2]
            if old and old in content:
                content = content.replace(old, new, 1)
                applied += 1
            else:
                failed.append(i)

    cart = CART.read_text(encoding="utf-8").strip()
    if cart and "cart-modal-overlay" not in content:
        content = content.rstrip() + "\n\n" + cart + "\n"

    OUT.write_text(content, encoding="utf-8")
    lines = content.count("\n") + 1
    print(f"Applied: {applied}, Failed: {len(failed)}")
    print(f"Output: {OUT} ({lines} lines, {len(content)} chars)")
    if failed:
        print(f"First failed op indices: {failed[:10]}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
