<div class="s-modern p-4" wire:ignore.self>
    @include('admin._partials.settings-modern')

    <style>
        .bots-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 18px;
        }
        .bots-header h2 {
            margin: 0; font-size: 20px; font-weight: 700; color: var(--sm-slate-800);
            display: inline-flex; align-items: center; gap: 10px;
        }
        .bots-header h2 i { color: var(--sm-primary); }
        .bots-header .sub { display: block; margin-top: 2px; font-size: 13px; color: var(--sm-slate-500); font-weight: 500; }

        .bot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
        .bot-card {
            background: #fff; border: 1px solid var(--sm-slate-200); border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 4px 16px rgba(15,23,42,.05);
            display: flex; flex-direction: column; gap: 10px;
        }
        .bot-card.inactive { opacity: 0.55; }
        .bot-card .bot-head {
            display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;
        }
        .bot-card .bot-name { font-size: 15px; font-weight: 700; color: var(--sm-slate-800); }
        .bot-card .bot-desc { font-size: 12.5px; color: var(--sm-slate-500); margin-top: 2px; }
        .bot-card .bot-meta {
            display: flex; align-items: center; gap: 8px;
            font-size: 11.5px; color: var(--sm-slate-500);
            flex-wrap: wrap;
        }
        .bot-tag {
            background: var(--sm-slate-100); color: var(--sm-slate-700);
            padding: 3px 9px; border-radius: 999px; font-weight: 600;
        }
        .bot-tag.active { background: rgba(16,185,129,.1); color: var(--sm-success); }
        .bot-tag.inactive { background: rgba(239,68,68,.1); color: var(--sm-danger); }

        .bot-actions { display: flex; gap: 6px; }
        .bot-actions button {
            background: transparent; border: 1px solid var(--sm-slate-200);
            padding: 5px 10px; border-radius: 7px; font-size: 12px; font-weight: 600;
            color: var(--sm-slate-600); cursor: pointer;
            font-family: inherit;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .bot-actions button:hover { background: var(--sm-slate-50); color: var(--sm-slate-800); }
        .bot-actions button.danger { color: var(--sm-danger); border-color: rgba(239,68,68,.3); }
        .bot-actions button.danger:hover { background: rgba(239,68,68,.08); }

        .bot-form-card {
            background: #fff; border: 1px solid var(--sm-slate-200); border-radius: 14px;
            padding: 20px 24px; box-shadow: 0 6px 24px rgba(15,23,42,.06);
            margin-bottom: 20px;
        }
        .bot-form-card h3 { margin: 0 0 16px; font-size: 16px; font-weight: 700; color: var(--sm-slate-800); }
        .bot-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .bot-field { display: flex; flex-direction: column; gap: 5px; }
        .bot-field.full { grid-column: 1 / -1; }
        .bot-field label { font-size: 12.5px; font-weight: 600; color: var(--sm-slate-700); }
        .bot-field label .req { color: var(--sm-danger); }
        .bot-field input[type="text"],
        .bot-field select,
        .bot-field textarea {
            background: #fff; border: 1px solid var(--sm-slate-200); border-radius: 8px;
            padding: 9px 12px; font-size: 13px; color: var(--sm-slate-800);
            font-family: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .bot-field input:focus, .bot-field select:focus, .bot-field textarea:focus {
            border-color: var(--sm-primary); outline: none; box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .bot-field textarea { resize: vertical; min-height: 90px; font-family: ui-monospace, Menlo, monospace; font-size: 12.5px; }
        .bot-field .hint { font-size: 11.5px; color: var(--sm-slate-500); }
        .bot-field .err { font-size: 11.5px; color: var(--sm-danger); }

        .bot-form-actions {
            margin-top: 18px; display: flex; justify-content: flex-end; gap: 10px;
        }
        .bot-form-actions .btn {
            padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600;
            border: 1px solid transparent; cursor: pointer; font-family: inherit;
            transition: background 0.15s ease, color 0.15s ease;
        }
        .btn-primary { background: var(--sm-primary); color: #fff; }
        .btn-primary:hover { background: var(--sm-primary-dark); }
        .btn-ghost { background: transparent; border-color: var(--sm-slate-200); color: var(--sm-slate-700); }
        .btn-ghost:hover { background: var(--sm-slate-50); }

        .test-box { margin-top: 14px; padding: 14px; background: var(--sm-slate-50); border-radius: 10px; border: 1px dashed var(--sm-slate-200); }
        .test-box textarea { width: 100%; }
        .test-box .test-output {
            margin-top: 10px; padding: 11px 14px; background: #fff;
            border: 1px solid var(--sm-slate-200); border-radius: 8px;
            font-size: 13px; color: var(--sm-slate-800); white-space: pre-wrap; min-height: 40px;
        }
        .test-box .test-run {
            background: var(--sm-primary); color: #fff; border: none;
            padding: 7px 14px; border-radius: 7px; font-size: 12.5px; font-weight: 600; cursor: pointer;
            margin-top: 8px;
        }

        .notice-bar {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 16px; background: #fff7ed; border: 1px solid #fdba74;
            color: #9a3412; border-radius: 10px; font-size: 13px; margin-bottom: 18px;
        }
        .flash-ok {
            padding: 10px 14px; background: rgba(16,185,129,.1); color: #047857;
            border: 1px solid rgba(16,185,129,.3); border-radius: 10px; font-size: 13px;
            margin-bottom: 16px;
        }

        .empty-state {
            text-align: center; padding: 60px 20px; color: var(--sm-slate-500);
            background: #fff; border: 1px dashed var(--sm-slate-200); border-radius: 14px;
        }
        .empty-state i { font-size: 42px; color: var(--sm-slate-300); margin-bottom: 10px; }
        .empty-state h4 { margin: 0 0 4px; font-size: 15px; color: var(--sm-slate-700); }
        .empty-state p { margin: 0; font-size: 13px; }
    </style>

    <div class="bots-header">
        <h2>
            <i class="fa fa-robot"></i>
            <span>
                Bots
                <span class="sub">AI assistants that help draft replies in Admin tools.</span>
            </span>
        </h2>
        @if(!$showForm)
            <button type="button" class="btn btn-primary"
                    style="padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;background:var(--sm-primary);color:#fff;border:none;cursor:pointer;"
                    wire:click="newBot">
                <i class="fa fa-plus"></i> Create bot
            </button>
        @endif
    </div>

    @if(!$grokConfigured)
        <div class="notice-bar">
            <i class="fa fa-exclamation-triangle" style="margin-top:1px;"></i>
            <div>
                <strong>Grok API key not configured.</strong>
                Set <code>GROK_API_KEY</code> in your <code>.env</code> to enable bot replies. You can still create bots, but they won't be able to generate drafts yet.
            </div>
        </div>
    @endif

    @if(session('bots-success'))
        <div class="flash-ok"><i class="fa fa-check-circle"></i> {{ session('bots-success') }}</div>
    @endif

    @if($showForm)
        <div class="bot-form-card">
            <h3>{{ $editingBotId ? 'Edit bot' : 'Create a new bot' }}</h3>

            <div class="bot-form-grid">
                <div class="bot-field">
                    <label>Name <span class="req">*</span></label>
                    <input type="text" wire:model="name" placeholder="e.g. Billing assistant">
                    @error('name') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="bot-field">
                    <label>Task <span class="req">*</span></label>
                    <select wire:model="task">
                        @foreach($tasks as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('task') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="bot-field full">
                    <label>Short description</label>
                    <input type="text" wire:model="description" placeholder="One-line summary shown in bot picker">
                    @error('description') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="bot-field full">
                    <label>System prompt <span class="req">*</span></label>
                    <textarea wire:model="systemPrompt" rows="6" placeholder="You are a polite support assistant for the Evoory listings site. Answer billing and account questions only. Keep replies under 3 sentences."></textarea>
                    <span class="hint">Defines the bot's personality and rules. The user will never see this directly.</span>
                    @error('systemPrompt') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="bot-field full">
                    <label>Knowledge base</label>
                    <textarea wire:model="knowledge" rows="8" placeholder="Paste FAQs, pricing, policies, canned responses, etc. The bot is told to never invent facts outside this block."></textarea>
                    <span class="hint">Free-text — the bot sees this every call. Keep it under ~20,000 characters.</span>
                    @error('knowledge') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="bot-field">
                    <label>Model</label>
                    <select wire:model="model">
                        <option value="grok-4-fast">grok-4-fast (cheap, fast)</option>
                        <option value="grok-4">grok-4 (higher quality)</option>
                    </select>
                </div>

                <div class="bot-field" style="justify-content:flex-end;">
                    <label>
                        <input type="checkbox" wire:model="isActive" style="margin-right:6px;">
                        Active (bot available to admins)
                    </label>
                </div>
            </div>

            <div class="bot-form-actions">
                <button type="button" class="btn btn-ghost" wire:click="cancelForm">Cancel</button>
                <button type="button" class="btn btn-primary" wire:click="saveBot">
                    {{ $editingBotId ? 'Save changes' : 'Create bot' }}
                </button>
            </div>
        </div>
    @endif

    @if($bots->isEmpty() && !$showForm)
        <div class="empty-state">
            <i class="fa fa-robot"></i>
            <h4>No bots yet</h4>
            <p>Create your first bot to start drafting replies with AI.</p>
        </div>
    @elseif(!$showForm)
        <div class="bot-grid">
            @foreach($bots as $bot)
                <div class="bot-card {{ $bot->is_active ? '' : 'inactive' }}" wire:key="bot-{{ $bot->id }}">
                    <div class="bot-head">
                        <div>
                            <div class="bot-name">{{ $bot->name }}</div>
                            @if($bot->description)
                                <div class="bot-desc">{{ $bot->description }}</div>
                            @endif
                        </div>
                        <div class="bot-actions">
                            <button type="button" wire:click="editBot({{ $bot->id }})" title="Edit bot">
                                <i class="fa fa-pencil-alt"></i>
                            </button>
                            <button type="button" wire:click="toggleActive({{ $bot->id }})" title="{{ $bot->is_active ? 'Disable' : 'Enable' }}">
                                <i class="fa {{ $bot->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                            </button>
                            <button type="button" class="danger" wire:click="deleteBot({{ $bot->id }})" wire:confirm="Delete bot '{{ $bot->name }}'?" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bot-meta">
                        <span class="bot-tag">{{ $tasks[$bot->task] ?? $bot->task }}</span>
                        <span class="bot-tag">{{ $bot->model }}</span>
                        <span class="bot-tag {{ $bot->is_active ? 'active' : 'inactive' }}">
                            {{ $bot->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </div>

                    <div class="test-box">
                        <label style="font-size:12px;font-weight:600;color:var(--sm-slate-700);">Test this bot</label>
                        <textarea wire:model="testInput" rows="2" placeholder="Type a sample customer question…"
                                  style="margin-top:6px;background:#fff;border:1px solid var(--sm-slate-200);border-radius:7px;padding:8px 10px;font-size:12.5px;"></textarea>
                        <button type="button" class="test-run" wire:click="runTest({{ $bot->id }})"
                                wire:loading.attr="disabled" wire:target="runTest">
                            <span wire:loading.remove wire:target="runTest">Run test</span>
                            <span wire:loading wire:target="runTest">Thinking…</span>
                        </button>
                        @if($testingBotId === $bot->id && $testOutput !== '')
                            <div class="test-output">{{ $testOutput }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
