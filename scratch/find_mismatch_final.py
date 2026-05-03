import sys

def find_mismatch(filename):
    with open(filename, 'r', encoding='utf-8') as f:
        lines = f.readlines()

    stack = []
    for i, line in enumerate(lines):
        import re
        tags = re.findall(r'<(/?div|section|/section|nav|/nav)[^>]*>', line)
        for tag in tags:
            tag_name = tag.split()[0].replace('/', '').replace('<', '').replace('>', '').lower()
            if tag.startswith('/'):
                if not stack:
                    print(f"Error: Found </{tag_name}> at line {i+1} with no matching <{tag_name}>")
                else:
                    last_tag, last_line = stack.pop()
                    if last_tag != tag_name:
                        print(f"Error: Mismatch at line {i+1}. Found </{tag_name}> but expected </{last_tag}> (opened at line {last_line+1})")
            else:
                stack.append((tag_name, i))

    for tag, line in stack:
        print(f"Error: <{tag}> at line {line+1} was never closed")

if __name__ == "__main__":
    find_mismatch("resources/views/user/detail-event-registered.blade.php")
