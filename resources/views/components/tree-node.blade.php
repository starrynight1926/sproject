@props(['node'])

@php
    $colors = [
        'green' => ['bg' => 'var(--green-dim)', 'text' => 'var(--green)', 'border' => 'rgba(34,211,160,0.3)'],
        'amber' => ['bg' => 'var(--amber-dim)', 'text' => 'var(--amber)', 'border' => 'rgba(245,158,11,0.3)'],
        'red' => ['bg' => 'var(--red-dim)', 'text' => 'var(--red)', 'border' => 'rgba(248,113,113,0.3)'],
        'blue' => ['bg' => 'var(--blue-dim)', 'text' => 'var(--blue)', 'border' => 'rgba(96,165,250,0.3)'],
        'purple' => ['bg' => 'var(--purple-dim)', 'text' => 'var(--purple)', 'border' => 'rgba(167,139,250,0.3)'],
    ];

    $boxStyles = [
        'root' => 'background:var(--bg3);border:2px solid var(--blue);border-radius:10px;padding:10px 18px;font-weight:700;font-size:14px;min-width:180px;max-width:340px;',
        'group' => 'background:var(--bg2);border:1px solid var(--border2);border-radius:8px;padding:8px 16px;font-weight:600;font-size:13px;color:var(--text2);min-width:140px;',
        'node' => 'background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:8px 14px;font-size:12px;min-width:160px;max-width:300px;',
        'leaf' => 'background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:6px 12px;font-size:11px;min-width:140px;max-width:260px;',
    ];
@endphp

<li>
    <div style="{{ $boxStyles[$node['level']] ?? $boxStyles['node'] }}display:inline-block;text-align:left;">
        <div style="color:var(--text);">{{ $node['label'] }}</div>
        @if(!empty($node['subtitle']))
            <div style="color:var(--text3);font-size:11px;margin-top:2px;">{{ $node['subtitle'] }}</div>
        @endif
        @if(!empty($node['badges']))
            <div style="display:flex;flex-direction:column;gap:4px;margin-top:4px;">
                @foreach($node['badges'] as $badge)
                    @php $c = $colors[$badge['color']] ?? $colors['blue']; @endphp
                    @if(isset($badge['percent']))
                        <div style="position:relative;border-radius:5px;background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }};overflow:hidden;min-width:160px;">
                            <div style="position:absolute;inset:0;width:{{ max(0, min(100, $badge['percent'])) }}%;background:{{ $c['text'] }};opacity:0.35;"></div>
                            <div style="position:relative;font-size:10px;padding:3px 8px;color:var(--text);white-space:normal;line-height:1.4;">
                                {{ $badge['text'] }}
                            </div>
                        </div>
                    @else
                        <span style="display:block;font-size:10px;padding:2px 8px;border-radius:5px;background:{{ $c['bg'] }};color:{{ $c['text'] }};border:1px solid {{ $c['border'] }};white-space:normal;line-height:1.4;">
                            {{ $badge['text'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    @if(!empty($node['children']))
        <ul>
            @foreach($node['children'] as $child)
                <x-tree-node :node="$child" />
            @endforeach
        </ul>
    @endif
</li>
