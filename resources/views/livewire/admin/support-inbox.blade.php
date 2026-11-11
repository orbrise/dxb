<div>
@push('css')
{{-- Load Vite so window.Echo is defined in the admin panel (admin master
     doesn't @vite by default; scoped here so no side-effects elsewhere). --}}
@vite(['resources/js/app.js'])
@include('admin._partials.settings-modern')
<style>
/* Support Inbox — styled to match the modern admin theme (indigo/slate light).
   Uses the same design tokens as admin._partials.settings-modern. */
/* 204px = header 68 + page title 62 + footer 60, leaving a 14px gap above
   the footer (the same as between the two panes). */
.s-modern .si-wrap {
    display: flex; height: calc(100vh - 204px); min-height: 520px; gap: 14px;
}

/* ---------- Left sidebar (customer list) ---------- */
.s-modern .si-side {
    width: 340px; min-width: 300px;
    background: #fff;
    border: 1px solid var(--sm-slate-200);
    border-radius: 14px;
    display: flex; flex-direction: column;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.s-modern .si-side-head { padding: 16px 16px 10px; border-bottom: 1px solid var(--sm-slate-100); }
.s-modern .si-view-tab-badge { min-width: 17px; padding: 1px 5px; border-radius: 99px; background: #ef4444; color: #fff; font-size: 10px; line-height: 16px; text-align: center; }
.s-modern .si-call-row-unanswered .si-name,
.s-modern .si-call-row-unanswered .si-preview { color: #b91c1c; }
.s-modern .si-call-row-status { display: inline-flex; align-items: center; gap: 5px; }
.s-modern .si-search {
    width: 100%; padding: 9px 12px;
    background: var(--sm-slate-50);
    border: 1px solid var(--sm-slate-200);
    border-radius: 8px;
    color: var(--sm-slate-800);
    font-size: 13px;
}
.s-modern .si-search:focus {
    outline: none;
    border-color: var(--sm-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}
.s-modern .si-search::placeholder { color: var(--sm-slate-500); }
/* Chat list filter chips (All / Unread / Archived), WhatsApp-style pills. */
.s-modern .si-chat-filters { display: flex; gap: 6px; margin-top: 10px; overflow-x: auto; scrollbar-width: none; }
.s-modern .si-chat-filters::-webkit-scrollbar { display: none; }
.s-modern .si-chat-filter {
    flex: none; display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border: 0; border-radius: 999px;
    background: var(--sm-slate-100); color: var(--sm-slate-600);
    font: inherit; font-size: 12.5px; font-weight: 600; line-height: 1.3; cursor: pointer;
}
.s-modern .si-chat-filter:hover { background: var(--sm-slate-200); color: var(--sm-slate-800); }
.s-modern .si-chat-filter.active { background: rgba(99,102,241,.14); color: var(--sm-primary-dark); }
.s-modern .si-chat-filter i { font-size: 11px; }
.s-modern .si-chat-filter small {
    min-width: 17px; padding: 0 5px; border-radius: 999px;
    background: var(--sm-slate-300, #cbd5e1); color: #fff; font-size: 10.5px; font-weight: 700; line-height: 17px; text-align: center;
}
.s-modern .si-chat-filter.active small { background: var(--sm-primary); }
.s-modern .si-archived-tag {
    display: inline-flex; align-items: center; gap: 3px; margin-left: 6px; padding: 1px 6px;
    border-radius: 4px; background: var(--sm-slate-100); color: var(--sm-slate-500);
    font-size: 10px; font-weight: 600; vertical-align: 1px;
}

.s-modern .si-list { flex: 1; overflow-y: auto; padding: 8px; }
.s-modern .si-item {
    display: flex; align-items: center;
    padding: 11px 12px;
    border-radius: 10px; cursor: pointer;
    transition: background .15s;
    margin-bottom: 4px;
}
.s-modern .si-item:hover { background: var(--sm-slate-50); }
.s-modern .si-item.active {
    background: rgba(99, 102, 241, 0.08);
}
/* --si-av: a customer's own colour (user_avatar_color()), set inline where
   the avatar is a customer's; everything else keeps the indigo. */
.s-modern .si-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: var(--si-av, linear-gradient(135deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%));
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 600; font-size: 14px;
    margin-right: 10px; overflow: hidden; flex-shrink: 0;
}
/* The photo sits over the initial; if it fails to load (onerror hides it),
   the initial shows instead of a broken-image icon. wire:ignore on the
   <img> keeps that hidden state through re-renders. */
.s-modern .si-avatar { position: relative; }
.s-modern .si-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
/* Chat rows: a green dot on the photo while the customer is online. The
   avatar clips its contents, so the dot sits on a wrapper instead. */
.s-modern .si-avatar-wrap { position: relative; flex-shrink: 0; margin-right: 10px; }
.s-modern .si-item .si-avatar-wrap > .si-avatar { margin-right: 0; }
.s-modern .si-online-dot {
    position: absolute; right: 0; bottom: 0;
    width: 11px; height: 11px; border-radius: 50%;
    background: #22c55e; box-shadow: 0 0 0 2px #fff;
}
.s-modern .si-info { flex: 1; min-width: 0; }
.s-modern .si-name { color: var(--sm-slate-800); font-weight: 600; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.s-modern .si-country-prefix { margin-left: 5px; color: var(--sm-slate-500); font-size: 10px; font-weight: 500; }
.s-modern .si-preview { color: var(--sm-slate-500); font-size: 12.5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.s-modern .si-preview.is-unread { color: var(--sm-slate-700); font-weight: 500; }
.s-modern .si-preview .si-ticks { margin: 0 3px 0 0; }
.s-modern .si-preview-icon { margin-right: 4px; color: var(--sm-slate-400); font-size: 11.5px; }
.s-modern .si-preview-icon.is-missed { color: #ef4444; }
.s-modern .si-preview-empty { color: var(--sm-slate-400); font-style: italic; }
.s-modern .si-meta { text-align: right; margin-left: 8px; flex-shrink: 0; }
.s-modern .si-time { color: var(--sm-slate-500); font-size: 10.5px; margin-bottom: 4px; }
.s-modern .si-badge {
    background: var(--sm-primary); color: #fff;
    font-size: 10.5px; font-weight: 600;
    padding: 2px 7px; border-radius: 999px;
}
/* Row chevron menu (Delete chat / Delete call): appears on hover like
   WhatsApp Web, always shown on touch screens. siRowMenu() drives it. */
.s-modern .si-meta-actions { display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
.s-modern .si-row-actions { position: relative; opacity: 0; transition: opacity .15s; }
.s-modern .si-item:hover .si-row-actions,
.s-modern .si-row-actions.open,
.s-modern .si-row-actions:focus-within { opacity: 1; }
@media (hover: none) { .s-modern .si-row-actions { opacity: 1; } }
.s-modern .si-row-actions-trigger {
    display: inline-flex; align-items: center; justify-content: center;
    width: 24px; height: 22px; padding: 0; border: 0; border-radius: 5px;
    background: transparent; color: var(--sm-slate-500); font-size: 11px; cursor: pointer;
}
.s-modern .si-row-actions-trigger:hover { background: var(--sm-slate-200); color: var(--sm-slate-700); }
.s-modern .si-row-actions-menu {
    display: none; position: absolute; top: calc(100% + 4px); right: 0; z-index: 50;
    width: 150px; padding: 4px; border: 1px solid var(--sm-slate-200); border-radius: 8px;
    background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.16);
}
/* No room below inside the scrolling list — open upward. */
.s-modern .si-row-actions.up .si-row-actions-menu { top: auto; bottom: calc(100% + 4px); }
.s-modern .si-row-actions.open .si-row-actions-menu { display: block; }

/* ---------- Right pane (message thread) ---------- */
.s-modern .si-main {
    flex: 1; display: flex; flex-direction: column;
    background: #fff;
    border: 1px solid var(--sm-slate-200);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
.s-modern .si-head {
    padding: 14px 20px;
    border-bottom: 1px solid var(--sm-slate-100);
    background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
    display: flex; align-items: center; justify-content: space-between;
}
.s-modern .si-head-name { color: var(--sm-slate-800); font-weight: 600; font-size: 15px; }
.s-modern .si-head-mail { color: var(--sm-slate-500); font-size: 12.5px; }
.s-modern .si-head-presence.is-online { color: #16a34a; font-weight: 600; }
/* WhatsApp-style wallpaper: beige with a faint doodle tile. */
.s-modern .si-msgs,
.s-modern .si-typing-row {
    background: #efeae2 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='240' height='240' viewBox='0 0 240 240'%3E%3Cg fill='none' stroke='%23000' stroke-opacity='.055' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 20h26a5 5 0 0 1 5 5v14a5 5 0 0 1-5 5H34l-8 6v-6h-4a5 5 0 0 1-5-5V25a5 5 0 0 1 5-5z'/%3E%3Cpath d='M152 50c-9-6-14-11-14-17a7 7 0 0 1 14-2 7 7 0 0 1 14 2c0 6-5 11-14 17z'/%3E%3Cpath d='M98 82l2.5 6.6 7 .3-5.5 4.4 1.9 6.8-5.9-3.9-5.9 3.9 1.9-6.8-5.5-4.4 7-.3z'/%3E%3Cpath d='M190 92l5 7c1 1.5.5 3.5-1 4.5l-2 1.5c2.5 5 6 8.5 11 11l1.5-2c1-1.5 3-2 4.5-1l7 5c1.5 1 1.5 3.5 0 5l-3 3c-3 3-8 3.5-12 1-9-5-16-12-21-21-2.5-4-2-9 1-12l3-3c1.5-1.5 4-1.5 5 0z'/%3E%3Ccircle cx='48' cy='150' r='11'/%3E%3Cpath d='M42 153a7 7 0 0 0 12 0'/%3E%3Cpath d='M118 162l30-12-9 28-6-10zM133 168l15-18'/%3E%3Crect x='182' y='176' width='28' height='19' rx='4'/%3E%3Ccircle cx='196' cy='185.5' r='5'/%3E%3Cpath d='M190 176l2.5-4h7l2.5 4'/%3E%3Cpath d='M84 224v-22l15-3v20'/%3E%3Ccircle cx='80.5' cy='224' r='3.5'/%3E%3Ccircle cx='95.5' cy='219' r='3.5'/%3E%3Crect x='12' y='84' width='28' height='19' rx='3'/%3E%3Cpath d='M12 87l14 9 14-9'/%3E%3Ccircle cx='214' cy='34' r='3'/%3E%3Ccircle cx='64' cy='104' r='2.5'/%3E%3Ccircle cx='160' cy='118' r='2'/%3E%3Ccircle cx='22' cy='212' r='3'/%3E%3Ccircle cx='232' cy='140' r='2'/%3E%3Ccircle cx='140' cy='220' r='2.5'/%3E%3C/g%3E%3Cg fill='%23000' fill-opacity='.055'%3E%3Ccircle cx='44' cy='147' r='1.3'/%3E%3Ccircle cx='52' cy='147' r='1.3'/%3E%3C/g%3E%3C/svg%3E") 0 0 / 240px 240px;
}
.s-modern .si-msgs {
    flex: 1; overflow-y: auto;
    padding: 16px 28px 20px;
    display: flex; flex-direction: column;
    gap: 2px;
}
/* Customer "typing…" bubble under the thread: same wallpaper and padding. */
.s-modern .si-typing-row {
    padding: 0 28px 12px;
    --ev-typing-bg: #fff;
    --ev-typing-dot: #8696a0;
}
.s-modern .si-msg { display: flex; max-width: 100%; }
.s-modern .si-msg.sent { justify-content: flex-end; }
.s-modern .si-msg.recv { justify-content: flex-start; }
/* A run of messages from one sender sits close together; a new run starts
   a little lower (is-first, set in the message loop). */
.s-modern .si-msg.is-first { margin-top: 10px; }
.s-modern .si-date-sep + .si-msg.is-first { margin-top: 0; }
.s-modern .si-bub {
    --si-bub-bg: #fff;
    position: relative;
    max-width: 65%;
    padding: 6px 9px 8px;
    border-radius: 8px;
    background: var(--si-bub-bg);
    box-shadow: 0 1px .5px rgba(11, 20, 26, .13);
    font-size: 14px; line-height: 19px;
}
/* The first bubble of a run gets the tail at its top corner. */
.s-modern .si-msg.is-first .si-bub::before {
    content: ""; position: absolute; top: 0;
    width: 8px; height: 13px; background: var(--si-bub-bg);
}
.s-modern .si-msg.is-first.sent .si-bub { border-top-right-radius: 0; }
.s-modern .si-msg.is-first.sent .si-bub::before { right: -8px; clip-path: polygon(0 0, 100% 0, 0 100%); }
.s-modern .si-msg.is-first.recv .si-bub { border-top-left-radius: 0; }
.s-modern .si-msg.is-first.recv .si-bub::before { left: -8px; clip-path: polygon(0 0, 100% 0, 100% 100%); }
.s-modern .si-msg.media .si-bub::before { display: none; }
/* Text bubbles: the time and ticks share the last line of text. A spacer
   after the text keeps room for them (it wraps to a new line when the
   last line is full); the time is pinned to the bottom-right corner. */
.s-modern .si-bub.has-text p::after { content: ""; display: inline-block; width: 56px; }
.s-modern .si-msg.sent .si-bub.has-text p::after { width: 76px; }
.s-modern .si-bub.has-text .si-btime { position: absolute; right: 8px; bottom: 4px; margin: 0; white-space: nowrap; }
/* Touch screens always show the ⋮ chevron, so keep text out from under it. */
@media (hover: none) { .s-modern .si-bub { padding-right: 30px; } }
.s-modern .si-msg-actions { position: absolute; z-index: 30; top: 2px; right: 2px; opacity: 0; }
.s-modern .si-msg-actions.open { z-index: 60; }
.s-modern .si-msg:hover .si-msg-actions,
.s-modern .si-msg-actions:focus-within { opacity: 1; }
@media (hover: none) { .s-modern .si-msg-actions { opacity: 1; } }
/* Shown over the text on hover, so it fades the text out beneath it
   (WhatsApp Web's treatment) instead of reserving an empty column. */
.s-modern .si-msg-actions-trigger {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 22px; padding: 0; border: 0; border-radius: 0 6px 0 0;
    background: radial-gradient(at top right, var(--si-bub-bg, #fff) 60%, transparent 80%);
    color: #8696a0; font-size: 11px; cursor: pointer;
}
.s-modern .si-msg-actions-trigger:hover { color: var(--sm-slate-700); }
.s-modern .si-msg.media .si-msg-actions-trigger { background: radial-gradient(at top right, rgba(11,20,26,.45), transparent 70%); color: #fff; }
.s-modern .si-msg-actions-menu {
    display: none; position: absolute; top: 27px; right: 0; z-index: 12;
    width: 142px; padding: 4px; border: 1px solid var(--sm-slate-200); border-radius: 8px;
    background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.16);
}
.s-modern .si-msg-actions.open .si-msg-actions-menu { display: block; }
.s-modern .si-msg-actions-menu button,
.s-modern .si-row-actions-menu button {
    display: flex; align-items: center; gap: 8px; width: 100%; padding: 8px;
    border: 0; border-radius: 5px; background: transparent; color: var(--sm-slate-700);
    font: inherit; font-size: 12px; text-align: left; cursor: pointer;
}
.s-modern .si-msg-actions-menu button:hover,
.s-modern .si-row-actions-menu button:hover { background: var(--sm-slate-100); }
.s-modern .si-msg-actions-menu button.danger,
.s-modern .si-row-actions-menu button.danger { color: #b91c1c; }
.s-modern .si-reply-quote { margin: 0 0 7px; padding: 5px 8px; border-left: 3px solid #53bdeb; border-radius: 3px; background: rgba(255,255,255,.55); font-size: 11px; }
.s-modern .si-reply-quote strong { display: block; margin-bottom: 2px; color: #416b7c; }
.s-modern .si-reply-quote span { display: block; color: var(--sm-slate-600); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-reply-quote[data-si-jump-to] { cursor: pointer; }
.s-modern .si-reply-quote[data-si-jump-to]:hover { background: rgba(83,189,235,.14); }
.s-modern .si-msg.si-jump-highlight .si-bub { animation: siMessageJump 1.4s ease-out; }
@keyframes siMessageJump { 0%, 35% { box-shadow: 0 0 0 3px rgba(83,189,235,.55); } 100% { box-shadow: 0 1px .5px rgba(11,20,26,.13); } }
/* WhatsApp-like pale green for agent messages, white for customer messages. */
.s-modern .si-msg.sent .si-bub { --si-bub-bg: #d9fdd3; color: #111b21; }
.s-modern .si-msg.recv .si-bub { --si-bub-bg: #fff; color: #111b21; }
.s-modern .si-msg.media .si-bub { padding: 3px; box-shadow: none; background: transparent !important; }
/* pre-wrap keeps the line breaks of multi-line messages (top-up message,
   quick replies), same as the customer's my-chat bubble. */
.s-modern .si-bub p { margin: 0; word-wrap: break-word; white-space: pre-wrap; }
.s-modern .si-date-sep { display: flex; justify-content: center; margin: 12px 0 8px; }
.s-modern .si-date-sep span {
    padding: 5px 12px; border-radius: 8px;
    background: #fff; color: #54656f; font-size: 12px; font-weight: 500;
    box-shadow: 0 1px .5px rgba(11,20,26,.13);
}
.s-modern .si-btime { font-size: 11px; line-height: 15px; margin-top: 3px; opacity: 0.7; text-align: right; }
.s-modern .si-starred { margin-right: 4px; color: #d6a400; }

/* Full-area overlay that covers si-main while the admin reviews starred
   messages. Mirrors my-chat's .contact-info-panel treatment visually. */
.s-modern .si-starred-panel {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    background: #fff;
    z-index: 20;
}
.s-modern .si-main { position: relative; }

/* ---------- Customer info (tap the name in the chat header) ---------- */
.s-modern .si-head-profile {
    display: flex; align-items: center; flex: 1; min-width: 0;
    margin: -4px 0 -4px -6px; padding: 4px 10px 4px 6px;
    border-radius: 12px; cursor: pointer; outline: none;
    transition: background .15s;
}
.s-modern .si-head-profile:hover,
.s-modern .si-head-profile:focus-visible { background: var(--sm-slate-50); }
/* Full-pane "Contact info" over the chat, laid out like my-chat's. */
.s-modern .si-info-panel {
    position: absolute; inset: 0; z-index: 25;
    display: flex; flex-direction: column;
    background: var(--sm-slate-50);
    animation: siInfoIn .16s ease-out;
}
@keyframes siInfoIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
.s-modern .si-info-head {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--sm-slate-100);
    background: #fff;
}
.s-modern .si-info-head h3 { margin: 0; font-size: 17px; font-weight: 700; color: var(--sm-slate-800); }
.s-modern .si-info-close {
    display: inline-flex; align-items: center; justify-content: center;
    width: 36px; height: 36px; border: 0; border-radius: 50%;
    background: transparent; color: var(--sm-slate-700); font-size: 17px; cursor: pointer;
}
.s-modern .si-info-close:hover { background: var(--sm-slate-100); color: var(--sm-slate-900, #0f172a); }
.s-modern .si-info-body { flex: 1; overflow-y: auto; }
.s-modern .si-info-inner { max-width: 820px; margin: 0 auto; padding-bottom: 24px; }
.s-modern .si-info-hero {
    display: flex; flex-direction: column; align-items: center; text-align: center;
    padding: 34px 20px 26px; background: #fff;
}
.s-modern .si-info-avatar {
    position: relative; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    width: 160px; height: 160px; margin-bottom: 18px; border-radius: 50%;
    background: var(--si-av, linear-gradient(135deg, #6366f1, #4f46e5));
    color: #fff; font-size: 62px; font-weight: 700;
    box-shadow: 0 8px 26px rgba(15, 23, 42, .16);
}
.s-modern .si-info-avatar a,
.s-modern .si-info-avatar img { position: absolute; inset: 0; width: 100%; height: 100%; }
.s-modern .si-info-avatar img { object-fit: cover; }
.s-modern .si-info-name { max-width: 100%; color: var(--sm-slate-800); font-size: 26px; font-weight: 700; line-height: 1.2; overflow-wrap: anywhere; }
.s-modern .si-info-mail { max-width: 100%; margin-top: 4px; color: var(--sm-slate-500); font-size: 14px; overflow-wrap: anywhere; }
.s-modern .si-info-seen { margin-top: 6px; color: var(--sm-slate-500); font-size: 14px; }
.s-modern .si-info-seen.online { color: #16a34a; font-weight: 600; }
.s-modern .si-info-seen.online::before {
    content: ""; display: inline-block; width: 8px; height: 8px; margin-right: 6px;
    border-radius: 50%; background: #22c55e; vertical-align: 1px;
}
.s-modern .si-info-chips { display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 12px; }
.s-modern .si-info-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 999px;
    background: var(--sm-slate-100); color: var(--sm-slate-600);
    font-size: 11.5px; font-weight: 600;
}
.s-modern .si-info-chip.good { background: #dcfce7; color: #15803d; }
.s-modern .si-info-chip.warn { background: #fef3c7; color: #b45309; }
.s-modern .si-info-chip.bad { background: #fee2e2; color: #b91c1c; }
.s-modern .si-info-chip.plan { background: #ede9fe; color: #6d28d9; }
/* Round icon buttons with a label underneath (Voice / Video / …). */
.s-modern .si-info-actions { display: flex; justify-content: center; gap: 30px; margin-top: 26px; }
.s-modern .si-info-action {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 0; border: 0; background: transparent; cursor: pointer;
    text-decoration: none !important;
}
.s-modern .si-info-action-icon {
    display: flex; align-items: center; justify-content: center;
    width: 58px; height: 58px; border-radius: 50%;
    border: 1px solid var(--sm-slate-200); background: #fff;
    color: var(--sm-primary); font-size: 20px;
    transition: background .15s, border-color .15s, color .15s;
}
.s-modern .si-info-action > span:last-child { color: var(--sm-slate-700); font-size: 13px; font-weight: 600; }
.s-modern .si-info-action:hover .si-info-action-icon { border-color: var(--sm-primary); background: var(--sm-primary); color: #fff; }
/* Tappable rows with a round icon, like my-chat's Contact info. */
.s-modern .si-info-rows { margin-top: 10px; background: #fff; }
.s-modern .si-info-row {
    display: flex; align-items: center; gap: 16px; width: 100%;
    padding: 14px 20px; border: 0; border-bottom: 1px solid var(--sm-slate-100);
    background: transparent; color: var(--sm-slate-800); font-family: inherit; font-size: 15.5px; text-align: left; cursor: pointer;
}
.s-modern .si-info-row:last-child { border-bottom: 0; }
.s-modern .si-info-row:hover { background: var(--sm-slate-50); }
.s-modern .si-info-row-icon {
    flex: none; display: flex; align-items: center; justify-content: center;
    width: 42px; height: 42px; border-radius: 50%;
    background: var(--sm-slate-100); color: var(--sm-slate-600); font-size: 16px;
}
.s-modern .si-info-row-text { flex: 1; min-width: 0; font-weight: 500; }
.s-modern .si-info-row-text small { display: block; margin-top: 1px; color: var(--sm-slate-500); font-size: 12.5px; font-weight: 400; }
.s-modern .si-info-row-meta { color: var(--sm-slate-500); font-size: 14px; }
.s-modern .si-info-row-meta.strong { color: var(--sm-slate-800); font-weight: 700; }
.s-modern .si-info-row-go { color: var(--sm-slate-400, #94a3b8); font-size: 12px; }
.s-modern .si-info-row.danger { color: #dc2626; }
.s-modern .si-info-row.danger .si-info-row-icon { background: #fee2e2; color: #dc2626; }
.s-modern .si-info-section { margin-top: 10px; padding: 16px 20px; background: #fff; }
.s-modern .si-info-section h4 {
    display: flex; align-items: center; justify-content: space-between;
    margin: 0 0 10px; color: var(--sm-slate-500);
    font-size: 11.5px; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
}
.s-modern .si-info-list { margin: 0; }
.s-modern .si-info-list > div {
    display: flex; justify-content: space-between; gap: 14px;
    padding: 9px 0; border-bottom: 1px solid var(--sm-slate-100); font-size: 14px;
}
.s-modern .si-info-list > div:last-child { border-bottom: 0; }
.s-modern .si-info-list dt { flex: none; color: var(--sm-slate-500); font-weight: 500; }
.s-modern .si-info-list dd { min-width: 0; margin: 0; color: var(--sm-slate-800); font-weight: 600; text-align: right; overflow-wrap: anywhere; }
.s-modern .si-info-list dd a { color: var(--sm-primary); }
.s-modern .si-info-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
.s-modern .si-info-stat { padding: 10px 12px; border-radius: 12px; background: var(--sm-slate-50); }
.s-modern .si-info-stat strong { display: block; color: var(--sm-slate-800); font-size: 18px; }
.s-modern .si-info-stat span { color: var(--sm-slate-500); font-size: 12px; }
.s-modern .si-info-stat.bad strong { color: #dc2626; }
.s-modern .si-info-note { margin-top: 10px; color: var(--sm-slate-500); font-size: 12.5px; }
.s-modern .si-info-about { margin: 0; color: var(--sm-slate-700); font-size: 13.5px; line-height: 1.5; white-space: pre-line; overflow-wrap: anywhere; }
.s-modern .si-info-search { position: relative; margin-bottom: 6px; }
.s-modern .si-info-search .fa {
    position: absolute; top: 50%; left: 14px; transform: translateY(-50%);
    color: var(--sm-slate-400, #94a3b8); font-size: 13px; pointer-events: none;
}
.s-modern .si-info-search input {
    display: block; width: 100%; height: 40px; padding: 0 14px 0 38px;
    border: 1px solid var(--sm-slate-200); border-radius: 999px; background: var(--sm-slate-50);
    color: var(--sm-slate-800); font-size: 13.5px;
}
.s-modern .si-info-search input:focus { outline: none; border-color: var(--sm-primary); background: #fff; box-shadow: 0 0 0 3px rgba(99, 102, 241, .12); }
.s-modern .si-info-listing-item { border-bottom: 1px solid var(--sm-slate-100); }
.s-modern .si-info-listing-item:last-of-type { border-bottom: 0; }
.s-modern .si-info-listing { display: flex; align-items: center; gap: 10px; padding: 9px 0; }
.s-modern .si-info-listing-main { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.s-modern .si-info-pkg {
    display: inline-flex; align-items: center; gap: 4px; margin-top: 3px;
    color: var(--sm-slate-500); font-size: 11.5px; font-weight: 600;
}
.s-modern .si-info-pkg.on { color: #6d28d9; }
.s-modern .si-info-pkg.off { color: #b45309; }
.s-modern .si-info-upgrade {
    flex: none; display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 11px; border: 1px solid var(--sm-primary); border-radius: 999px;
    background: #fff; color: var(--sm-primary); font-size: 12px; font-weight: 700; cursor: pointer;
    transition: background .15s, color .15s;
}
.s-modern .si-info-upgrade:hover { background: var(--sm-primary); color: #fff; }
.s-modern .si-info-upgrade.is-open { border-color: var(--sm-slate-300, #cbd5e1); color: var(--sm-slate-600); }
.s-modern .si-info-upgrade.is-open:hover { background: var(--sm-slate-100); color: var(--sm-slate-800); }
.s-modern .si-info-notice {
    display: flex; align-items: flex-start; gap: 8px; margin-bottom: 10px; padding: 10px 12px;
    border-radius: 10px; background: #dcfce7; color: #166534; font-size: 12.5px; font-weight: 600;
}
.s-modern .si-info-notice .fa { margin-top: 2px; }

/* Upgrade form, opened in place under its listing */
.s-modern .si-upgrade-form {
    margin: 0 0 12px; padding: 14px; border-radius: 12px;
    background: var(--sm-slate-50); border: 1px solid var(--sm-slate-200);
    animation: siInfoIn .16s ease-out;
}
.s-modern .si-upgrade-warn { margin-bottom: 12px; color: #b45309; font-size: 12.5px; font-weight: 600; }
.s-modern .si-upgrade-hint { margin-bottom: 8px; color: var(--sm-slate-500); font-size: 12px; }
.s-modern .si-upgrade-label { display: block; margin: 0 0 6px; color: var(--sm-slate-700); font-size: 12.5px; font-weight: 700; }
.s-modern .si-upgrade-options { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
.s-modern .si-upgrade-opt { position: relative; margin: 0; cursor: pointer; }
.s-modern .si-upgrade-opt input { position: absolute; opacity: 0; pointer-events: none; }
.s-modern .si-upgrade-opt > span {
    display: flex; flex-direction: column; min-width: 86px; padding: 8px 12px;
    border: 1.5px solid var(--sm-slate-200); border-radius: 10px; background: #fff;
    color: var(--sm-slate-800); font-size: 13px; font-weight: 700; line-height: 1.3;
    transition: border-color .15s, background .15s;
}
.s-modern .si-upgrade-opt > span small { color: var(--sm-slate-500); font-size: 11.5px; font-weight: 500; }
.s-modern .si-upgrade-opt:hover > span { border-color: var(--sm-primary); }
.s-modern .si-upgrade-opt input:checked + span { border-color: var(--sm-primary); background: rgba(99, 102, 241, .07); box-shadow: 0 0 0 1px var(--sm-primary); }
.s-modern .si-upgrade-opt input:disabled + span { opacity: .45; cursor: not-allowed; }
.s-modern .si-upgrade-pay { display: grid; gap: 8px; margin-bottom: 14px; }
.s-modern .si-upgrade-pay .si-upgrade-opt > span { min-width: 0; }
.s-modern .si-upgrade-form textarea,
.s-modern .si-upgrade-form .si-upgrade-days {
    display: block; width: 100%; padding: 10px 12px;
    border: 1px solid var(--sm-slate-200); border-radius: 9px; background: #fff;
    color: var(--sm-slate-800); font-size: 13.5px;
}
.s-modern .si-upgrade-form .si-upgrade-days { max-width: 220px; margin: -4px 0 14px; }
.s-modern .si-upgrade-form textarea { min-height: 150px; margin-bottom: 4px; line-height: 1.45; resize: vertical; }
.s-modern .si-upgrade-form textarea:focus,
.s-modern .si-upgrade-form .si-upgrade-days:focus { outline: none; border-color: var(--sm-primary); box-shadow: 0 0 0 3px rgba(99, 102, 241, .12); }
.s-modern .si-info-listing-img {
    flex: none; display: flex; align-items: center; justify-content: center;
    width: 44px; height: 54px; overflow: hidden; border-radius: 8px;
    background: var(--sm-slate-100); color: var(--sm-slate-400, #94a3b8);
}
.s-modern .si-info-listing-img img { width: 100%; height: 100%; object-fit: cover; }
.s-modern .si-info-listing-text { flex: 1; min-width: 0; }
.s-modern .si-info-listing-name { overflow: hidden; color: var(--sm-slate-800); font-size: 14px; font-weight: 600; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-info-listing-sub { color: var(--sm-slate-500); font-size: 12.5px; }
.s-modern .si-info-listing > .fa { color: var(--sm-slate-400, #94a3b8); font-size: 12px; }

.s-modern .si-starred-head {
    display: flex; align-items: center; gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--sm-slate-100);
    background: var(--sm-slate-50);
}
.s-modern .si-starred-back {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    border: none; border-radius: 50%;
    background: transparent; color: var(--sm-slate-600); cursor: pointer;
    font-size: 18px;
}
.s-modern .si-starred-back:hover { background: var(--sm-slate-100); color: var(--sm-slate-800); }
.s-modern .si-starred-head h3 { margin: 0; font-size: 15px; font-weight: 700; color: var(--sm-slate-800); }
.s-modern .si-starred-body { flex: 1; overflow-y: auto; }
.s-modern .si-starred-list { padding: 4px 0; }
.s-modern .si-starred-item {
    display: block; width: 100%; text-align: left;
    padding: 12px 18px;
    border: none; border-bottom: 1px solid var(--sm-slate-100);
    background: transparent; cursor: pointer;
    font-family: inherit;
}
.s-modern .si-starred-item:hover { background: var(--sm-slate-50); }
.s-modern .si-starred-meta {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: var(--sm-slate-500);
    margin-bottom: 4px;
}
.s-modern .si-starred-sender { color: var(--sm-slate-800); font-weight: 600; }
.s-modern .si-starred-arrow { color: var(--sm-slate-400); }
.s-modern .si-starred-peer { color: var(--sm-slate-600); }
.s-modern .si-starred-date { margin-left: auto; }
.s-modern .si-starred-body-line { font-size: 13.5px; color: var(--sm-slate-800); line-height: 1.3; }
.s-modern .si-starred-body-line i { color: var(--sm-slate-400); margin-right: 6px; }
.s-modern .si-msg.sent .si-btime { color: #667781; }
.s-modern .si-msg.recv .si-btime { color: var(--sm-slate-500); }
.s-modern .si-msg.sent .si-bub .si-agent {
    font-size: 10.5px; font-weight: 700;
    color: #667781; opacity: 1;
    display: block; margin-bottom: 2px;
}

/* ---------- Input row ---------- */
.s-modern .si-input {
    padding: 12px 16px;
    background: #fff;
    border-top: 1px solid var(--sm-slate-100);
    display: flex; gap: 8px; align-items: center;
}
.s-modern .si-input textarea {
    flex: 1;
    padding: 10px 16px;
    background: var(--sm-slate-50);
    border: 1px solid var(--sm-slate-200);
    border-radius: 21px;
    color: var(--sm-slate-800);
    font-size: 14px;
    resize: none;
    min-height: 42px; max-height: 120px;
}
.s-modern .si-input textarea:focus {
    outline: none;
    border-color: var(--sm-primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}
.s-modern .si-input button[type="submit"] {
    width: 42px; height: 42px; border-radius: 50%; border: none;
    background: linear-gradient(180deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%);
    color: #fff; cursor: pointer; font-size: 15px;
    box-shadow: 0 3px 8px rgba(99, 102, 241, 0.25);
    transition: transform .12s, box-shadow .12s;
}
.s-modern .si-input button[type="submit"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.32);
}
.s-modern .si-input button[type="submit"]:disabled {
    background: var(--sm-slate-200); color: var(--sm-slate-500);
    cursor: not-allowed; box-shadow: none; transform: none;
}
.s-modern .si-empty {
    flex: 1;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    color: var(--sm-slate-500); text-align: center; padding: 40px;
}
.s-modern .si-empty i { font-size: 44px; color: var(--sm-slate-300); margin-bottom: 14px; }
.s-modern .si-empty h4 { color: var(--sm-slate-700); margin: 0 0 6px; font-size: 16px; font-weight: 600; }
.s-modern .si-empty p { color: var(--sm-slate-500); font-size: 13px; max-width: 280px; }

/* WebSocket state indicator next to "Customers" title */
.s-modern .si-conn { font-size: 10px; margin-left: 8px; }
.s-modern .si-conn.online { color: var(--sm-success); }
.s-modern .si-conn.offline { color: var(--sm-slate-500); animation: si-pulse 1.5s infinite ease-in-out; }
@keyframes si-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }

.s-modern .si-ticks { display: inline-flex; align-items: center; margin-left: 3px; vertical-align: -1px; }
.s-modern .si-ticks .t { display: block; color: #667781; }
.s-modern .si-ticks .t.read { color: #53bdeb; }

/* Scrollbars in soft slate to match the theme */
.s-modern .si-msgs::-webkit-scrollbar,
.s-modern .si-list::-webkit-scrollbar { width: 6px; }
.s-modern .si-msgs::-webkit-scrollbar-track,
.s-modern .si-list::-webkit-scrollbar-track { background: transparent; }
.s-modern .si-msgs::-webkit-scrollbar-thumb,
.s-modern .si-list::-webkit-scrollbar-thumb { background: var(--sm-slate-300); border-radius: 3px; }
.s-modern .si-msgs::-webkit-scrollbar-thumb:hover,
.s-modern .si-list::-webkit-scrollbar-thumb:hover { background: var(--sm-slate-500); }

/* ---------- Media inside messages ---------- */
.s-modern .si-msg-image {
    display: block; max-width: 260px; max-height: 300px;
    border-radius: 6px; margin: 0; cursor: pointer;
}
.s-modern .si-msg.media video { display: block; margin: 0; border-radius: 6px !important; }
.s-modern .si-msg-voice {
    display: inline-flex; align-items: center; gap: 12px; min-width: 260px; max-width: 340px;
    box-sizing: border-box; padding: 10px 16px 18px 12px; border-radius: 14px;
    background: #1C1F21; color: #fff;
}
.s-modern .si-msg-voice audio { display: none; }
.s-modern .si-vp-play {
    display: inline-flex; align-items: center; justify-content: center; flex: 0 0 36px;
    width: 36px; height: 36px; padding: 0; border: 1.5px solid rgba(255,255,255,.85);
    border-radius: 50%; background: transparent; color: #fff; cursor: pointer;
    transition: background 0.15s ease, border-color 0.15s ease;
}
.s-modern .si-vp-play:hover { background: rgba(255,255,255,0.08); }
.s-modern .si-vp-play svg { width: 14px; height: 14px; display: block; margin-left: 2px; }
.s-modern .si-vp-play.is-playing svg { margin-left: 0; }
.s-modern .si-vp-body { position: relative; flex: 1 1 auto; min-width: 0; height: 26px; }
.s-modern .si-msg-voice { width: min(360px, 100%); }
.s-modern .si-vp-waveform { display: flex; align-items: center; gap: 2px; height: 100%; cursor: pointer; touch-action: none; }
.s-modern .si-vp-bar { flex: 1 1 0; min-width: 2px; border-radius: 2px; background: rgba(255,255,255,.35); transition: background-color 60ms linear; pointer-events: none; }
.s-modern .si-vp-bar.is-played { background: #fff; }
.s-modern .si-vp-time { position: absolute; top: calc(100% + 4px); left: 0; color: rgba(255,255,255,.6); font-size: 11.5px; line-height: 1; font-variant-numeric: tabular-nums; }
.s-modern .si-msg-file {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 10px;
    background: rgba(255, 255, 255, 0.12);
    border-radius: 8px;
    color: inherit; text-decoration: none;
    min-width: 200px;
}
.s-modern .si-msg.recv .si-msg-file {
    background: var(--sm-slate-100);
    color: var(--sm-slate-800);
}
.s-modern .si-msg-file i { font-size: 20px; }
.s-modern .si-msg-file .fname { font-weight: 600; font-size: 13px; word-break: break-all; }
.s-modern .si-msg-file .fsize { font-size: 11px; opacity: 0.7; }

/* ---------- Call records inside messages ---------- */
.s-modern .si-call {
    display: flex; align-items: center; gap: 11px;
    min-width: 196px;
    margin: 2px 0;
}
.s-modern .si-call-icon {
    width: 34px; height: 34px; flex: none;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px;
    background: var(--sm-slate-100);
    color: var(--sm-slate-700);
}
.s-modern .si-msg.sent .si-call-icon {
    background: rgba(17, 27, 33, .08);
    color: #41525d;
}
.s-modern .si-call.missed .si-call-icon {
    background: rgba(239, 68, 68, 0.14);
    color: #dc2626;
}
.s-modern .si-msg.sent .si-call.missed .si-call-icon {
    background: #fee2e2;
    color: #b91c1c;
}
.s-modern .si-call-body { flex: 1; min-width: 0; }
.s-modern .si-call-label { font-size: 13px; font-weight: 600; }
.s-modern .si-call-sub { font-size: 11px; opacity: 0.72; margin-top: 2px; }
.s-modern .si-call-back {
    width: 32px; height: 32px; flex: none;
    border-radius: 50%; border: none; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 13px;
    background: var(--sm-slate-100); color: var(--sm-slate-700);
    transition: background .15s, transform .12s;
}
.s-modern .si-call-back:hover { background: var(--sm-slate-200); transform: translateY(-1px); }
.s-modern .si-msg.sent .si-call-back { background: rgba(17,27,33,.08); color: #41525d; }
.s-modern .si-msg.sent .si-call-back:hover { background: rgba(17,27,33,.14); }
.s-modern .si-wallet-receipt { min-width: 220px; padding: 10px 12px; border: 1px solid rgba(148,163,184,.35); border-radius: 8px; background: rgba(255,255,255,.92); color: var(--sm-slate-800); }
.s-modern .si-wallet-receipt-title { display: flex; align-items: center; gap: 7px; padding-bottom: 7px; margin-bottom: 7px; border-bottom: 1px solid var(--sm-slate-200); font-size: 12px; font-weight: 700; }
.s-modern .si-wallet-receipt-line { margin-top: 3px; font-size: 11.5px; overflow-wrap: anywhere; }
.s-modern .si-topup-balance { margin: 0 0 12px; padding: 10px 12px; border-radius: 8px; background: var(--sm-slate-50); color: var(--sm-slate-600); font-size: 12px; }
.s-modern .si-pending-msg .si-bub { padding: 4px; }
.s-modern .si-pending-media {
    position: relative; display: block; width: 240px; max-width: 100%; min-height: 180px;
    aspect-ratio: 4 / 3; overflow: hidden; border-radius: 9px;
    background: #111827 center / cover no-repeat;
}
.s-modern .si-pending-media.video { width: 220px; height: 300px; min-height: 0; aspect-ratio: 11 / 15; }
.s-modern .si-pending-media::after { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,.42); }
.s-modern .si-pending-media.failed::after { background: rgba(185,28,28,.58); }
.s-modern .si-pending-ring {
    position: absolute; z-index: 2; top: 50%; left: 50%; width: 64px; height: 64px;
    display: flex; align-items: center; justify-content: center; transform: translate(-50%, -50%);
}
.s-modern .si-pending-ring svg { position: absolute; inset: 0; width: 100%; height: 100%; transform: rotate(-90deg); }
.s-modern .si-pending-ring circle { fill: none; stroke-width: 5; stroke-linecap: round; }
.s-modern .si-pending-ring .bg { stroke: rgba(255,255,255,.25); }
.s-modern .si-pending-ring .fg { stroke: #c1f11d; stroke-dasharray: 182; stroke-dashoffset: 182; transition: stroke-dashoffset 150ms linear; }
.s-modern .si-pending-percent { position: relative; z-index: 1; color: #fff; font-size: 12px; font-weight: 700; }
.s-modern .si-pending-label { position: absolute; z-index: 2; right: 6px; bottom: 10px; left: 6px; color: #fff; text-align: center; font-size: 12px; font-weight: 600; text-shadow: 0 1px 2px #000; }

/* ---------- Thread header actions (dial + call block) ---------- */
.s-modern .si-head-actions { display: flex; align-items: center; gap: 8px; }
.s-modern .si-hbtn {
    height: 36px; width: 36px;
    border-radius: 50%;
    border: 1px solid var(--sm-slate-200);
    background: #fff; color: var(--sm-slate-700);
    cursor: pointer; font-size: 14px;
    display: inline-flex; align-items: center; justify-content: center;
    transition: background .15s, color .15s, border-color .15s, transform .12s;
}
.s-modern .si-hbtn:hover {
    background: var(--sm-primary); border-color: var(--sm-primary);
    color: #fff; transform: translateY(-1px);
}
/* ⋮ menu in the chat header: Top up, Block calls, Delete chat history. */
.s-modern .si-head-more { position: relative; }
.s-modern .si-head-more-menu {
    display: none; position: absolute; top: 42px; right: 0; z-index: 70;
    min-width: 200px; padding: 5px;
    border: 1px solid var(--sm-slate-200); border-radius: 10px; background: #fff;
    box-shadow: 0 12px 32px rgba(15, 23, 42, .18);
}
.s-modern .si-head-more.open .si-head-more-menu { display: block; }
.s-modern .si-head-more-menu button {
    display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 10px;
    border: 0; border-radius: 6px; background: transparent;
    color: var(--sm-slate-800); font: inherit; font-size: 13px; text-align: left; cursor: pointer;
}
.s-modern .si-head-more-menu button:hover { background: var(--sm-slate-100); }
.s-modern .si-head-more-menu button i { width: 16px; color: var(--sm-slate-500); text-align: center; }
.s-modern .si-head-more-menu button.danger,
.s-modern .si-head-more-menu button.danger i { color: #b91c1c; }
.s-modern .si-head-flag {
    display: flex; flex: none; align-items: center; gap: 8px; padding: 7px 20px;
    border-bottom: 1px solid #fecaca; background: #fef2f2; color: #b91c1c; font-size: 12.5px;
}
.s-modern .si-flag { color: #dc2626; font-size: 11px; margin-left: 6px; }

/* ---------- Attach menu ---------- */
.s-modern .si-att-wrap { position: relative; }
.s-modern .si-attach-menu {
    display: none;
    position: absolute; bottom: 46px; left: 0;
    min-width: 190px; padding: 6px;
    background: #fff;
    border: 1px solid var(--sm-slate-200);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.16);
    z-index: 60;
}
.s-modern .si-attach-menu.open { display: block; }
.s-modern .si-attach-menu button {
    display: flex; align-items: center; gap: 10px;
    width: 100%; padding: 9px 10px;
    background: transparent; border: none; border-radius: 8px;
    color: var(--sm-slate-800); font-size: 13px; font-weight: 500;
    cursor: pointer; text-align: left; font-family: inherit;
}
.s-modern .si-attach-menu button:hover { background: var(--sm-slate-100); }
.s-modern .si-attach-menu button i {
    width: 20px; text-align: center; font-size: 14px; color: var(--sm-primary);
}

/* ---------- Contact sheet ---------- */
.s-modern .si-modal {
    display: none;
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, 0.5);
    align-items: center; justify-content: center;
    z-index: 100001; padding: 20px;
}
.s-modern .si-modal.open { display: flex; }
.s-modern .si-modal-card {
    width: 100%; max-width: 340px;
    background: #fff; border-radius: 14px; padding: 20px;
    box-shadow: 0 20px 50px rgba(15, 23, 42, 0.3);
}
.s-modern .si-modal-card h5 { margin: 0 0 14px; font-size: 15px; color: var(--sm-slate-800); }
.s-modern .si-modal-card input:not([type="checkbox"]):not([type="radio"]):not([type="file"]) {
    display: block; width: 100%;
    margin-bottom: 10px; padding: 10px 12px;
    border: 1px solid var(--sm-slate-200); border-radius: 9px;
    font-size: 14px; color: var(--sm-slate-800); background: var(--sm-slate-50);
}
.s-modern .si-modal-card input:not([type="checkbox"]):not([type="radio"]):not([type="file"]):focus {
    outline: none; border-color: var(--sm-primary); background: #fff;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
}
.s-modern .si-modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 6px; }
.s-modern .si-modal-actions button {
    padding: 8px 16px; border-radius: 8px; border: none;
    font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;
}
.s-modern .si-modal-actions .ghost { background: transparent; color: var(--sm-slate-500); }
.s-modern .si-modal-actions .primary {
    background: linear-gradient(180deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%);
    color: #fff;
}
.s-modern .si-modal-card.si-broadcast-card {
    width: min(560px, 100%); max-width: 560px; max-height: 90vh;
    overflow-y: auto; overscroll-behavior: contain; box-sizing: border-box;
}
.s-modern .si-broadcast-card h5 { display: flex; align-items: center; gap: 8px; }
.s-modern .si-broadcast-close {
    width: 32px; height: 32px; margin: -7px -7px -7px auto; padding: 0;
    display: inline-flex; align-items: center; justify-content: center;
    border: 0; border-radius: 50%; background: transparent;
    color: var(--sm-slate-500); cursor: pointer; font-size: 15px;
}
.s-modern .si-broadcast-close:hover { background: var(--sm-slate-100); color: var(--sm-slate-800); }
.s-modern .si-broadcast-audiences { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); grid-auto-rows: minmax(88px, 1fr); align-items: stretch; gap: 8px; margin-bottom: 14px; }
.s-modern .si-broadcast-audiences label {
    display: flex; align-items: center; gap: 10px; min-width: 0; min-height: 88px; box-sizing: border-box; padding: 12px;
    border: 1px solid var(--sm-slate-200); border-radius: 8px; cursor: pointer;
    color: var(--sm-slate-700); font-size: 13px;
}
.s-modern .si-broadcast-audiences label:has(input:checked) { border-color: var(--sm-primary); background: rgba(99, 102, 241, .06); }
.s-modern .si-broadcast-audiences input,
.s-modern .si-broadcast-user input,
.s-modern .si-broadcast-confirm input {
    display: block; width: 16px; min-width: 16px; height: 16px; margin: 0;
    padding: 0; flex: 0 0 16px; accent-color: var(--sm-primary);
}
.s-modern .si-broadcast-audiences label > span { display: block; min-width: 0; line-height: 1.4; text-align: left; }
.s-modern .si-broadcast-audiences small { color: var(--sm-slate-500); font-size: 11px; }
.s-modern .si-broadcast-location { margin: 4px 0 14px; }
.s-modern .si-broadcast-location > label { display: block; margin: 0 0 6px; color: var(--sm-slate-700); font-size: 12px; font-weight: 600; }
.s-modern .si-broadcast-location select {
    display: block; width: 100%; height: 40px; margin: 0 0 10px; padding: 0 11px;
    border: 1px solid var(--sm-slate-200); border-radius: 8px; background: var(--sm-slate-50);
    color: var(--sm-slate-800); font: inherit; font-size: 13px;
}
.s-modern .si-broadcast-location select:focus { outline: none; border-color: var(--sm-primary); box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
.s-modern .si-broadcast-city { display: flex; align-items: center; gap: 10px; min-width: 0; padding: 9px 11px; border-bottom: 1px solid var(--sm-slate-100); cursor: pointer; }
.s-modern .si-broadcast-city:last-child { border-bottom: 0; }
.s-modern .si-broadcast-city input { display: block; width: 16px; min-width: 16px; height: 16px; margin: 0; padding: 0; flex: 0 0 16px; accent-color: var(--sm-primary); }
.s-modern .si-broadcast-city span { flex: 1; min-width: 0; color: var(--sm-slate-800); font-size: 12.5px; }
.s-modern .si-broadcast-users { max-height: 220px; overflow-y: auto; margin: 8px 0 14px; border: 1px solid var(--sm-slate-200); border-radius: 8px; }
.s-modern .si-broadcast-user { display: flex; align-items: center; gap: 10px; min-width: 0; padding: 10px 12px; border-bottom: 1px solid var(--sm-slate-100); cursor: pointer; }
.s-modern .si-broadcast-user:last-child { border-bottom: 0; }
.s-modern .si-broadcast-user span { flex: 1; min-width: 0; color: var(--sm-slate-800); font-size: 12.5px; overflow-wrap: anywhere; }
.s-modern .si-broadcast-user small { display: block; color: var(--sm-slate-500); overflow-wrap: anywhere; }
.s-modern .si-broadcast-count, .s-modern .si-broadcast-warning { margin: 8px 0; color: var(--sm-slate-500); font-size: 12px; }
.s-modern .si-broadcast-warning { padding: 10px 12px; border-radius: 8px; background: #fff7ed; color: #9a3412; }
.s-modern .si-broadcast-confirm { display: flex; align-items: flex-start; gap: 9px; margin: 12px 0 16px; color: var(--sm-slate-700); font-size: 12px; }
.s-modern .si-broadcast-card textarea { width: 100%; min-height: 110px; resize: vertical; padding: 10px 12px; border: 1px solid var(--sm-slate-200); border-radius: 8px; color: var(--sm-slate-800); }
.s-modern .si-broadcast-tools { display: flex; align-items: center; gap: 8px; margin: 10px 0 8px; }
.s-modern .si-broadcast-tool {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    width: auto; height: 36px; min-height: 36px; box-sizing: border-box;
    margin: 0; padding: 0 10px; border: 1px solid var(--sm-slate-200);
    border-radius: 7px; background: #fff; color: var(--sm-slate-700);
    font: inherit; font-size: 12px; line-height: 1; vertical-align: top; white-space: nowrap; cursor: pointer;
}
.s-modern .si-broadcast-tool:hover { border-color: var(--sm-primary); color: var(--sm-primary); }
.s-modern .si-broadcast-tool input { display: none; }
.s-modern .si-broadcast-emoji-wrap { position: relative; display: flex; align-items: center; height: 36px; flex: 0 0 auto; }
.s-modern .si-broadcast-emoji-menu {
    display: none; width: 100%; height: 360px; margin: 8px 0 0; overflow: hidden;
    flex-direction: column;
    border: 1px solid var(--sm-slate-200); border-radius: 9px; background: #fff;
    box-shadow: 0 4px 14px rgba(15, 23, 42, .08);
}
.s-modern .si-broadcast-emoji-menu.open { display: flex; }
.s-modern .si-broadcast-emoji-search {
    display: block; flex: none; width: auto; height: 36px; margin: 8px 8px 0; padding: 0 10px;
    border: 1px solid var(--sm-slate-200); border-radius: 7px; background: var(--sm-slate-50);
    color: var(--sm-slate-800); font: inherit; font-size: 12px;
}
.s-modern .si-broadcast-emoji-search:focus { outline: none; border-color: var(--sm-primary); box-shadow: 0 0 0 3px rgba(99,102,241,.1); }
.s-modern .si-broadcast-emoji-categories { display: flex; flex: none; gap: 4px; padding: 7px; overflow-x: auto; border-bottom: 1px solid var(--sm-slate-100); }
.s-modern .si-broadcast-emoji-categories button {
    width: 34px; height: 32px; flex: 0 0 34px; padding: 0; border: 0; border-radius: 6px;
    background: transparent; color: var(--sm-slate-600); font-size: 18px; cursor: pointer;
}
.s-modern .si-broadcast-emoji-categories button:hover,
.s-modern .si-broadcast-emoji-categories button.active { background: var(--sm-slate-100); color: var(--sm-primary); }
.s-modern .si-broadcast-emoji-grid {
    display: grid; flex: 1; min-height: 0; grid-template-columns: repeat(auto-fill, minmax(36px, 1fr));
    align-content: start; gap: 3px; padding: 8px; overflow-y: auto;
}
.s-modern .si-broadcast-emoji-grid button {
    width: 36px; height: 36px; padding: 0; border: 0; border-radius: 6px;
    background: transparent; font-size: 22px; line-height: 1; cursor: pointer;
}
.s-modern .si-broadcast-emoji-grid button:hover { background: var(--sm-slate-100); }
.s-modern .si-broadcast-emoji-empty { grid-column: 1 / -1; padding: 20px; color: var(--sm-slate-500); text-align: center; font-size: 13px; }
.s-modern .si-broadcast-emoji-icon { font-size: 16px; line-height: 1; }
.s-modern .si-broadcast-image-preview { display: flex; align-items: center; gap: 10px; margin: 8px 0; padding: 8px; border: 1px solid var(--sm-slate-200); border-radius: 8px; }
.s-modern .si-broadcast-image-preview img { width: 56px; height: 56px; object-fit: cover; border-radius: 6px; }
.s-modern .si-broadcast-image-preview span { flex: 1; min-width: 0; color: var(--sm-slate-700); font-size: 12px; overflow-wrap: anywhere; }
.s-modern .si-broadcast-image-preview button { border: 0; background: transparent; color: #b91c1c; cursor: pointer; }
.s-modern .si-broadcast-notice { margin: 0 0 12px; padding: 9px 12px; border-radius: 7px; background: #ecfdf5; color: #047857; font-size: 13px; }
@media (max-width: 575px) {
    .s-modern .si-broadcast-audiences { grid-template-columns: 1fr; }
    .s-modern .si-modal { padding: 12px; }
    .s-modern .si-modal-card.si-broadcast-card { padding: 16px; }
}

/* ---------- Attachment / mic controls ---------- */
.s-modern .si-input .si-att-btn,
.s-modern .si-input .si-mic-btn {
    width: 38px; height: 38px; border-radius: 50%; border: none;
    background: transparent;
    color: var(--sm-slate-500);
    cursor: pointer; font-size: 15px;
    transition: background .15s, color .15s;
}
.s-modern .si-input .si-att-btn:hover,
.s-modern .si-input .si-mic-btn:hover {
    background: var(--sm-slate-100);
    color: var(--sm-primary);
}
.s-modern .si-input .si-mic-btn.recording {
    background: var(--sm-danger); color: #fff; animation: siRecPulse 1s infinite;
}

/* ---------- AI suggestion (bots) controls ---------- */
.s-modern .si-input .si-suggest-wrap { position: relative; }
.s-modern .si-input .si-suggest-btn {
    width: 38px; height: 38px; border-radius: 50%; border: none;
    background: transparent; color: var(--sm-primary);
    cursor: pointer; font-size: 15px;
    transition: background .15s, color .15s;
}
.s-modern .si-input .si-suggest-btn:hover {
    background: rgba(99,102,241,.1); color: var(--sm-primary-dark);
}
.s-modern .si-input .si-suggest-btn:disabled { opacity: 0.6; cursor: wait; }

.s-modern .si-suggest-menu {
    position: absolute; bottom: calc(100% + 8px); right: 0;
    min-width: 240px; max-width: 320px;
    background: #fff;
    border: 1px solid var(--sm-slate-200);
    border-radius: 10px;
    box-shadow: 0 12px 36px rgba(15,23,42,.15);
    padding: 6px;
    z-index: 50;
    display: none;
}
.s-modern .si-suggest-wrap.open .si-suggest-menu { display: block; }
.s-modern .si-suggest-menu-head {
    padding: 6px 10px; font-size: 11px; letter-spacing: .06em;
    text-transform: uppercase; color: var(--sm-slate-500); font-weight: 700;
}
.s-modern .si-suggest-menu button {
    display: block; width: 100%; text-align: left;
    padding: 8px 10px; border: none; background: transparent;
    border-radius: 7px; cursor: pointer;
    font-family: inherit;
    transition: background .15s;
}
.s-modern .si-suggest-menu button:hover { background: var(--sm-slate-50); }
.s-modern .si-suggest-menu button strong {
    display: block; font-size: 13px; font-weight: 600; color: var(--sm-slate-800);
}
.s-modern .si-suggest-menu button span {
    display: block; font-size: 11.5px; color: var(--sm-slate-500); margin-top: 2px;
}

/* Suggested-reply chip above the composer. */
.s-modern .si-suggest-chip {
    margin: 0 16px 10px;
    padding: 12px 14px;
    background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 100%);
    border: 1px solid var(--sm-primary); border-radius: 12px;
    color: var(--sm-slate-800);
}
.s-modern .si-suggest-head {
    display: flex; justify-content: space-between; align-items: center; gap: 10px;
    margin-bottom: 8px;
}
.s-modern .si-suggest-label {
    font-size: 12px; font-weight: 700; color: var(--sm-primary);
    display: inline-flex; align-items: center; gap: 6px;
}
.s-modern .si-suggest-actions { display: flex; gap: 6px; }
.s-modern .si-suggest-actions button {
    border: 1px solid var(--sm-slate-200); background: #fff;
    padding: 5px 11px; border-radius: 7px; font-size: 12px; font-weight: 600;
    color: var(--sm-slate-600); cursor: pointer;
    font-family: inherit;
    transition: background .15s, color .15s;
}
.s-modern .si-suggest-actions button:hover {
    background: var(--sm-slate-50); color: var(--sm-slate-800);
}
.s-modern .si-suggest-body {
    font-size: 13.5px; line-height: 1.5; color: var(--sm-slate-800);
    white-space: pre-wrap;
}
@keyframes siRecPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
    50%      { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
}
/* Self-contained dark pill matching my-chat's .voice-recording-bar. The
   form around it stays light; the bar carries its own background so the
   look is identical regardless of host form theme. */
.s-modern .si-rec-bar {
    display: none;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    background: #1C1F21;
    border-radius: 999px;
    color: #fff;
    font-size: 13px;
    flex: 1;
    min-width: 0;
}
.s-modern .si-rec-bar.active { display: flex; }
.s-modern .si-rec-bar .dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: #ef4444;
    animation: siRecPulse 1s ease-in-out infinite;
    flex: 0 0 auto;
}
.s-modern .si-rec-bar.paused .dot { animation: none; opacity: 0.4; }
.s-modern .si-rec-bar #siRecTime {
    font-variant-numeric: tabular-nums;
    font-weight: 600;
    min-width: 40px;
    color: #fff;
}
.s-modern .si-rec-bar .rec-spacer { flex: 1 1 auto; }
.s-modern .si-rec-bar .rec-btn {
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    background: transparent; border: none; border-radius: 50%;
    color: rgba(255,255,255,0.7); cursor: pointer; padding: 0;
    transition: background 0.15s ease, color 0.15s ease;
    flex: 0 0 auto;
}
.s-modern .si-rec-bar .rec-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }
.s-modern .si-rec-bar .rec-btn svg { width: 18px; height: 18px; display: block; }
.s-modern .si-rec-bar .rec-btn.delete { color: #ef4444; }
.s-modern .si-rec-bar .rec-btn.delete:hover { background: rgba(239,68,68,0.12); color: #ef4444; }
.s-modern .si-rec-bar .rec-btn.send {
    background: #22c55e; color: #fff;
    width: 38px; height: 38px;
}
.s-modern .si-rec-bar .rec-btn.send:hover { background: #16a34a; color: #fff; }
/* Hide the normal composer affordances while the bar is active so the
   pill owns the full composer width — matches my-chat behavior. */
.s-modern .si-input form:has(.si-rec-bar.active) .si-att-wrap,
.s-modern .si-input form:has(.si-rec-bar.active) #siReplyInput,
.s-modern .si-input form:has(.si-rec-bar.active) #siMicBtn,
.s-modern .si-input form:has(.si-rec-bar.active) button[type="submit"] { display: none !important; }

/* ---------- Upload preview ---------- */
.s-modern .si-att-preview {
    display: none; align-items: center; gap: 12px;
    padding: 9px 16px;
    background: var(--sm-slate-50);
    border-top: 1px solid var(--sm-slate-100);
}
.s-modern .si-att-preview.open { display: flex; }
.s-modern .si-upload-thumb {
    position: relative; width: 52px; height: 52px; flex: 0 0 52px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; border-radius: 7px; background: var(--sm-slate-200); color: var(--sm-slate-600);
}
.s-modern .si-upload-thumb img { display: none; width: 100%; height: 100%; object-fit: cover; }
.s-modern .si-upload-thumb.has-preview img { display: block; }
.s-modern .si-upload-thumb.has-preview i { display: none; }
.s-modern .si-upload-thumb i { font-size: 18px; }
.s-modern .si-att-preview .info { flex: 1; min-width: 0; color: var(--sm-slate-800); font-size: 12.5px; }
.s-modern .si-upload-meta { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 6px; }
.s-modern .si-upload-meta span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-upload-meta strong { flex: none; color: var(--sm-primary); font-size: 12px; font-variant-numeric: tabular-nums; }
.s-modern .si-upload-track { height: 4px; overflow: hidden; border-radius: 99px; background: var(--sm-slate-200); }
.s-modern .si-upload-track span { display: block; width: 0; height: 100%; border-radius: inherit; background: var(--sm-primary); transition: width .12s linear; }
.s-modern .si-att-preview.is-error .si-upload-meta strong { color: var(--sm-danger); }
.s-modern .si-att-preview.is-error .si-upload-track span { background: var(--sm-danger); }
.s-modern .si-reply-composer { display: flex; align-items: center; gap: 10px; padding: 8px 14px; border-top: 1px solid var(--sm-slate-100); background: #fff; }
.s-modern .si-reply-composer-copy { min-width: 0; flex: 1; padding-left: 9px; border-left: 3px solid #53bdeb; }
.s-modern .si-reply-composer-copy strong { display: block; color: var(--sm-slate-700); font-size: 11px; }
.s-modern .si-reply-composer-copy span { display: block; color: var(--sm-slate-500); font-size: 11px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-reply-composer button { border: 0; background: transparent; color: var(--sm-slate-500); cursor: pointer; }
.s-modern .si-forward-targets { max-height: 300px; margin: 8px 0 12px; overflow-y: auto; border: 1px solid var(--sm-slate-200); border-radius: 8px; }
.s-modern .si-forward-target { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-bottom: 1px solid var(--sm-slate-100); cursor: pointer; }
.s-modern .si-forward-target:last-child { border-bottom: 0; }
.s-modern .si-forward-target input { width: 16px; height: 16px; accent-color: var(--sm-primary); }
.s-modern .si-forward-target span { min-width: 0; color: var(--sm-slate-800); font-size: 12px; }
.s-modern .si-forward-target small { display: block; color: var(--sm-slate-500); }
.s-modern .si-forward-card { max-width: 440px; }

/* ---------- Bottom icon strip (Chat · Calls · Media · Settings) ----------
   Mirrors my-chat's .chat-sidebar-tabs, recoloured for the admin theme. */
.s-modern .si-bottom-tabs {
    flex: none; display: flex; justify-content: space-around; align-items: center;
    padding: 10px 8px; border-top: 1px solid var(--sm-slate-200); background: var(--sm-slate-50);
}
.s-modern .si-bottom-tab {
    position: relative; width: 35px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 0; border-radius: 999px; background: transparent; color: var(--sm-slate-500);
    cursor: pointer; transition: background .12s, color .12s;
}
.s-modern .si-bottom-tab:hover { background: var(--sm-slate-100); color: var(--sm-slate-800); }
.s-modern .si-bottom-tab.active { background: var(--sm-primary); color: #fff; box-shadow: 0 3px 8px rgba(99,102,241,.25); }
.s-modern .si-bottom-tab svg { display: block; width: 15px; height: 15px; }
.s-modern .si-tab-badge {
    position: absolute; top: -4px; right: -3px; min-width: 17px; height: 17px; padding: 0 4px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 999px; background: #ef4444; color: #fff;
    font-size: 10px; font-weight: 700; line-height: 1; box-shadow: 0 0 0 2px var(--sm-slate-50);
}
.s-modern .si-tab-dot { position: absolute; top: 6px; right: 9px; width: 8px; height: 8px; border-radius: 50%; background: #ef4444; box-shadow: 0 0 0 2px var(--sm-slate-50); }
.s-modern .si-calls-paused {
    display: flex; align-items: center; gap: 8px; width: 100%; margin: 0 0 10px; padding: 8px 10px;
    border: 1px solid #fecaca; border-radius: 8px; background: #fef2f2; color: #b91c1c;
    font: inherit; font-size: 12px; text-align: left; cursor: pointer;
}
.s-modern .si-calls-paused span { flex: 1; }
.s-modern .si-calls-paused:hover { background: #fee2e2; }

/* ---------- Settings (left list + right pane), modelled on my-chat ---------- */
.s-modern .si-set-profile { display: flex; align-items: center; gap: 12px; padding: 8px 6px 16px; margin-bottom: 6px; border-bottom: 1px solid var(--sm-slate-100); }
.s-modern .si-set-profile .si-avatar { width: 50px; height: 50px; margin: 0; font-size: 18px; }
.s-modern .si-set-profile-name { color: var(--sm-slate-800); font-size: 15px; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-set-profile-sub { color: var(--sm-slate-500); font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-set-row {
    display: flex; align-items: center; gap: 12px; width: 100%; margin-bottom: 2px; padding: 11px 10px;
    border: 0; border-radius: 10px; background: transparent; font: inherit; text-align: left; cursor: pointer;
    transition: background .12s;
}
.s-modern .si-set-row:hover { background: var(--sm-slate-50); }
.s-modern .si-set-row.active { background: rgba(99,102,241,.08); box-shadow: inset 3px 0 0 0 var(--sm-primary); }
.s-modern .si-set-row-icon { width: 34px; height: 34px; flex: none; display: inline-flex; align-items: center; justify-content: center; border-radius: 9px; background: var(--sm-slate-100); color: var(--sm-slate-600); font-size: 14px; }
.s-modern .si-set-row.active .si-set-row-icon { background: var(--sm-primary); color: #fff; }
.s-modern .si-set-row-body { flex: 1; min-width: 0; }
.s-modern .si-set-row-title { display: block; color: var(--sm-slate-800); font-size: 13.5px; font-weight: 600; }
.s-modern .si-set-row-sub { display: block; margin-top: 1px; color: var(--sm-slate-500); font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-set-row-sub.danger { color: #b91c1c; }
.s-modern .si-set-row-arrow { color: var(--sm-slate-300); font-size: 13px; }
.s-modern .si-pane { flex: 1; min-height: 0; display: flex; flex-direction: column; }
.s-modern .si-pane-head {
    flex: none; display: flex; align-items: center; gap: 12px; padding: 14px 20px;
    border-bottom: 1px solid var(--sm-slate-100); background: linear-gradient(180deg, #fff 0%, #fbfbff 100%);
}
.s-modern .si-pane-back {
    width: 34px; height: 34px; flex: none; display: inline-flex; align-items: center; justify-content: center;
    border: 0; border-radius: 50%; background: transparent; color: var(--sm-slate-600); cursor: pointer; font-size: 15px;
}
.s-modern .si-pane-back:hover { background: var(--sm-slate-100); color: var(--sm-slate-800); }
.s-modern .si-pane-title { color: var(--sm-slate-800); font-size: 15px; font-weight: 700; }
.s-modern .si-pane-sub { color: var(--sm-slate-500); font-size: 12.5px; }
.s-modern .si-pane-body { flex: 1; min-height: 0; overflow-y: auto; padding: 20px 24px 32px; }
.s-modern .si-pane-inner { max-width: 720px; }
.s-modern .si-set-hint { margin: 0 0 14px; color: var(--sm-slate-500); font-size: 12.5px; }
.s-modern .si-set-card { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 12px; padding: 16px; border: 1px solid var(--sm-slate-200); border-radius: 12px; background: #fff; }
.s-modern .si-set-card-icon { width: 40px; height: 40px; flex: none; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; background: rgba(99,102,241,.1); color: var(--sm-primary); font-size: 16px; }
.s-modern .si-set-card-icon.danger { background: #fef2f2; color: #dc2626; }
.s-modern .si-set-card-body { flex: 1; min-width: 0; }
.s-modern .si-set-card-title { color: var(--sm-slate-800); font-size: 14px; font-weight: 700; }
.s-modern .si-set-card-text { margin-top: 3px; color: var(--sm-slate-500); font-size: 12.5px; line-height: 1.45; }
.s-modern .si-set-card-actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
.s-modern .si-set-note { margin-top: 10px; padding: 8px 10px; border-radius: 8px; background: #fff7ed; color: #9a3412; font-size: 12px; }
.s-modern .si-set-btn {
    display: inline-flex; align-items: center; gap: 7px; height: 34px; padding: 0 13px;
    border: 1px solid var(--sm-slate-200); border-radius: 8px; background: #fff; color: var(--sm-slate-700);
    font: inherit; font-size: 12.5px; font-weight: 600; white-space: nowrap; text-decoration: none; cursor: pointer;
}
.s-modern .si-set-btn:hover { border-color: var(--sm-primary); color: var(--sm-primary); text-decoration: none; }
.s-modern .si-set-btn.primary { border-color: var(--sm-primary); background: var(--sm-primary); color: #fff; }
.s-modern .si-set-btn.primary:hover { background: var(--sm-primary-dark); color: #fff; }
.s-modern .si-switch { position: relative; flex: none; width: 42px; height: 24px; margin: 8px 0 0; cursor: pointer; }
.s-modern .si-switch input { position: absolute; width: 0; height: 0; opacity: 0; }
.s-modern .si-switch span { position: absolute; inset: 0; border-radius: 999px; background: var(--sm-slate-300); transition: background .15s; }
.s-modern .si-switch span::after { content: ""; position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; border-radius: 50%; background: #fff; box-shadow: 0 1px 2px rgba(15,23,42,.2); transition: transform .15s; }
.s-modern .si-switch input:checked + span { background: var(--sm-success); }
.s-modern .si-switch input:checked + span::after { transform: translateX(18px); }
.s-modern .si-switch input:focus-visible + span { box-shadow: 0 0 0 3px rgba(99,102,241,.25); }
.s-modern .si-blocked-list { overflow: hidden; border: 1px solid var(--sm-slate-200); border-radius: 12px; }
.s-modern .si-blocked-row { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-bottom: 1px solid var(--sm-slate-100); background: #fff; }
.s-modern .si-blocked-row:last-child { border-bottom: 0; }
.s-modern .si-blocked-row .si-avatar { margin: 0; }
.s-modern .si-blocked-actions { display: flex; flex: none; gap: 6px; }

/* ---------- Status (left list + right stage), modelled on my-chat ---------- */
.s-modern .si-status-me { display: flex; align-items: center; gap: 12px; width: 100%; padding: 8px 6px 12px; border: 0; background: transparent; font: inherit; text-align: left; cursor: pointer; }
.s-modern .si-status-me-avatar { position: relative; flex: none; }
.s-modern .si-status-me-avatar .si-avatar { width: 50px; height: 50px; margin: 0; font-size: 18px; }
.s-modern .si-status-me-avatar.has-status .si-avatar { box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--sm-success); }
.s-modern .si-status-me-plus {
    position: absolute; right: -2px; bottom: -2px; width: 20px; height: 20px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 2px solid #fff; border-radius: 50%; background: var(--sm-success); color: #fff; font-size: 9px;
}
.s-modern .si-status-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; padding: 0 4px 12px; margin-bottom: 6px; border-bottom: 1px solid var(--sm-slate-100); }
.s-modern .si-status-actions .si-set-btn { justify-content: center; }
.s-modern .si-section-label { padding: 6px 8px 6px; color: var(--sm-slate-500); font-size: 11px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
.s-modern .si-status-thumb {
    position: relative; width: 40px; height: 40px; flex: none; margin-right: 10px; overflow: hidden;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50%; background: var(--sm-slate-100); color: #fff; font-size: 13px;
}
.s-modern .si-status-thumb img,
.s-modern .si-status-thumb video { width: 100%; height: 100%; object-fit: cover; }
.s-modern .si-status-thumb-play { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(15,23,42,.35); font-size: 10px; }
.s-modern .si-status-views { display: inline-flex; align-items: center; gap: 4px; color: var(--sm-slate-500); font-size: 11px; }
.s-modern .si-status-hint { padding: 12px 8px; color: var(--sm-slate-500); font-size: 11.5px; line-height: 1.45; }
.s-modern .si-status-body { flex: 1; min-height: 0; display: flex; }
.s-modern .si-status-stage {
    flex: 1; min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;
    padding: 24px; overflow-y: auto; background: #0f172a;
}
.s-modern .si-status-stage img,
.s-modern .si-status-stage video { display: block; max-width: 100%; max-height: calc(100vh - 380px); min-height: 0; border-radius: 10px; object-fit: contain; }
.s-modern .si-status-card {
    width: min(340px, 100%); aspect-ratio: 9 / 14; display: flex; align-items: center; justify-content: center;
    padding: 28px; border-radius: 14px; color: #fff; font-size: 22px; font-weight: 600; line-height: 1.35;
    text-align: center; white-space: pre-wrap; overflow-wrap: anywhere; box-shadow: 0 10px 30px rgba(0,0,0,.3);
}
.s-modern .si-status-card textarea {
    width: 100%; height: 100%; padding: 0; border: 0; outline: 0; resize: none; background: transparent;
    color: #fff; font: inherit; text-align: center;
}
.s-modern .si-status-card textarea::placeholder { color: rgba(255,255,255,.6); }
.s-modern .si-status-caption { max-width: 520px; color: #e2e8f0; font-size: 14px; text-align: center; white-space: pre-wrap; }
.s-modern .si-status-swatches { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
.s-modern .si-status-swatch { width: 28px; height: 28px; padding: 0; border: 2px solid rgba(255,255,255,.35); border-radius: 50%; cursor: pointer; }
.s-modern .si-status-swatch.active { border-color: #fff; box-shadow: 0 0 0 2px rgba(255,255,255,.35); }
.s-modern .si-status-composer-foot { display: flex; align-items: center; gap: 10px; }
.s-modern .si-status-count { color: #94a3b8; font-size: 12px; font-variant-numeric: tabular-nums; }
.s-modern .si-status-error { color: #fca5a5; font-size: 12.5px; }
.s-modern .si-status-viewers { width: 280px; flex: none; display: flex; flex-direction: column; border-left: 1px solid var(--sm-slate-100); background: #fff; }
.s-modern .si-status-viewers-head { flex: none; display: flex; align-items: center; gap: 8px; padding: 14px 16px; border-bottom: 1px solid var(--sm-slate-100); color: var(--sm-slate-800); font-size: 13px; font-weight: 700; }
.s-modern .si-status-viewers-list { flex: 1; min-height: 0; overflow-y: auto; padding: 6px; }
.s-modern .si-status-viewer { display: flex; align-items: center; gap: 10px; padding: 8px; }
.s-modern .si-status-viewer .si-avatar { width: 34px; height: 34px; margin: 0; font-size: 12px; }
.s-modern .si-status-viewer-time { margin-left: auto; color: var(--sm-slate-500); font-size: 11px; white-space: nowrap; }
.s-modern .si-pane-head-actions { display: flex; gap: 4px; margin-left: auto; }
.si-stp-overlay {
    position: fixed; inset: 0; z-index: 100002; display: flex; align-items: center; justify-content: center;
    padding: 20px; background: rgba(15,23,42,.6);
}
.si-stp-card { width: min(460px, 100%); max-height: 92vh; display: flex; flex-direction: column; gap: 12px; padding: 18px; overflow-y: auto; border-radius: 14px; background: #fff; box-shadow: 0 20px 50px rgba(15,23,42,.3); }
.si-stp-card h5 { margin: 0; color: #1e293b; font-size: 15px; }
.si-stp-media { display: flex; justify-content: center; border-radius: 10px; background: #0f172a; }
.si-stp-media img,
.si-stp-media video { display: block; max-width: 100%; max-height: 50vh; border-radius: 10px; }
.si-stp-card input[type="text"] { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 9px; background: #f8fafc; color: #1e293b; font-size: 14px; }
.si-stp-card input[type="text"]:focus { outline: none; border-color: #6366f1; background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
.si-stp-progress { height: 4px; overflow: hidden; border-radius: 99px; background: #e2e8f0; }
.si-stp-progress span { display: block; width: 0; height: 100%; background: #6366f1; transition: width .12s linear; }
.si-stp-error { color: #b91c1c; font-size: 12.5px; }
.si-stp-actions { display: flex; justify-content: flex-end; gap: 8px; }
.si-stp-actions button { padding: 8px 16px; border: 0; border-radius: 8px; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer; }
.si-stp-actions .ghost { background: transparent; color: #64748b; }
.si-stp-actions .primary { background: #6366f1; color: #fff; }
.si-stp-actions button:disabled { opacity: .6; cursor: wait; }

/* ---------- Settings → Account ---------- */
.s-modern .si-set-profile.is-link { width: 100%; border-top: 0; border-right: 0; border-left: 0; background: transparent; font: inherit; text-align: left; cursor: pointer; border-radius: 10px 10px 0 0; }
.s-modern .si-set-profile.is-link:hover { background: var(--sm-slate-50); }
.s-modern .si-acct-avatar-wrap { padding: 4px 0 22px; text-align: center; }
.s-modern .si-acct-avatar {
    position: relative; width: 120px; height: 120px; margin: 0 auto;
    display: flex; align-items: center; justify-content: center; border-radius: 50%;
    background: linear-gradient(135deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%); color: #fff; font-size: 44px; font-weight: 700;
}
.s-modern .si-acct-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
.s-modern .si-acct-avatar-btn {
    position: absolute; right: 2px; bottom: 2px; width: 38px; height: 38px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 3px solid #fff; border-radius: 50%; background: var(--sm-primary); color: #fff; font-size: 14px; cursor: pointer;
}
.s-modern .si-acct-avatar-btn:hover { background: var(--sm-primary-dark); }
.s-modern .si-acct-avatar.is-uploading::after {
    content: ""; position: absolute; inset: 0; border-radius: 50%;
    background: rgba(15,23,42,.45) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 50 50'%3E%3Ccircle cx='25' cy='25' r='20' fill='none' stroke='%23fff' stroke-width='4' stroke-dasharray='90 40'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='0.8s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/svg%3E") center / 40px no-repeat;
}
.s-modern .si-acct-caption { margin-top: 10px; color: var(--sm-slate-500); font-size: 12.5px; }
.s-modern .si-acct-caption.error { color: #b91c1c; }
.s-modern .si-field { margin-bottom: 16px; }
.s-modern .si-field > label { display: block; margin: 0 0 6px; color: var(--sm-slate-700); font-size: 12.5px; font-weight: 600; }
.s-modern .si-field-input {
    display: block; width: 100%; height: 42px; padding: 0 12px;
    border: 1px solid var(--sm-slate-200); border-radius: 9px; background: var(--sm-slate-50);
    color: var(--sm-slate-800); font: inherit; font-size: 14px;
}
.s-modern .si-field-input:focus { outline: none; border-color: var(--sm-primary); background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
.s-modern .si-field-input:disabled { color: var(--sm-slate-500); cursor: not-allowed; }
.s-modern .si-field-hint { margin-top: 5px; color: var(--sm-slate-500); font-size: 11.5px; }
.s-modern .si-phone-row { display: flex; gap: 8px; }
.s-modern .si-phone-cc { position: relative; width: 92px; flex: none; }
.s-modern .si-phone-cc span { position: absolute; top: 11px; left: 12px; color: var(--sm-slate-500); font-size: 14px; pointer-events: none; }
.s-modern .si-phone-cc .si-field-input { padding-left: 24px; }
.s-modern .si-msg.sent .si-bub .si-agent.si-agent-auto { color: var(--sm-primary); }
.s-modern .si-field-input.si-field-textarea { height: auto; min-height: 110px; padding: 10px 12px; line-height: 1.45; resize: vertical; }
.s-modern .si-pill { display: inline-block; margin-left: 6px; padding: 1px 8px; border-radius: 999px; background: var(--sm-slate-100); color: var(--sm-slate-600); font-size: 11px; font-weight: 700; vertical-align: 1px; }
.s-modern .si-pill.on { background: #dcfce7; color: #15803d; }
.s-modern .si-acct-saved { display: inline-flex; align-items: center; gap: 6px; margin-left: 10px; color: var(--sm-success); font-size: 12.5px; font-weight: 600; }

/* ---------- Quick replies (Settings list, editor modal, composer picker) ---------- */
.s-modern .si-qr-list { overflow: hidden; border: 1px solid var(--sm-slate-200); border-radius: 12px; }
.s-modern .si-qr-row { display: flex; align-items: flex-start; gap: 12px; padding: 12px 14px; border-bottom: 1px solid var(--sm-slate-100); background: #fff; }
.s-modern .si-qr-row:last-child { border-bottom: 0; }
.s-modern .si-qr-badge {
    flex: none; max-width: 160px; padding: 3px 8px; overflow: hidden; border-radius: 6px;
    background: rgba(99,102,241,.1); color: var(--sm-primary);
    font-family: SFMono-Regular, Consolas, monospace; font-size: 12px; font-weight: 600; text-overflow: ellipsis; white-space: nowrap;
}
.s-modern .si-qr-row-text {
    flex: 1; min-width: 0; color: var(--sm-slate-700); font-size: 13px; line-height: 1.45; white-space: pre-line;
    display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.s-modern .si-qr-row-actions { display: flex; flex: none; gap: 2px; }
.s-modern .si-qr-icon-btn {
    width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;
    border: 0; border-radius: 50%; background: transparent; color: var(--sm-slate-500); cursor: pointer;
}
.s-modern .si-qr-icon-btn:hover { background: var(--sm-slate-100); color: var(--sm-primary); }
.s-modern .si-qr-icon-btn.danger:hover { background: #fef2f2; color: #dc2626; }
.s-modern .si-modal-card.si-qr-card { max-width: 480px; }
.s-modern .si-qr-card label { display: block; margin: 0 0 6px; color: var(--sm-slate-700); font-size: 12px; font-weight: 600; }
.s-modern .si-qr-shortcut-field { position: relative; }
.s-modern .si-qr-shortcut-field > span { position: absolute; top: 10px; left: 12px; color: var(--sm-slate-500); font-size: 14px; font-weight: 600; pointer-events: none; }
.s-modern .si-qr-card textarea {
    display: block; width: 100%; min-height: 130px; margin: 0; padding: 10px 12px; resize: vertical;
    border: 1px solid var(--sm-slate-200); border-radius: 9px; background: var(--sm-slate-50); color: var(--sm-slate-800); font-size: 14px;
}
.s-modern .si-qr-card textarea:focus { outline: none; border-color: var(--sm-primary); background: #fff; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
.s-modern .si-qr-card-hint { margin: 6px 0 10px; color: var(--sm-slate-500); font-size: 11.5px; }
.s-modern .si-qr-card code,
.s-modern .si-set-hint code { padding: 1px 5px; border-radius: 4px; background: var(--sm-slate-100); color: var(--sm-slate-700); font-size: 11.5px; }
.s-modern .si-input { position: relative; }
.s-modern .si-qr-menu {
    display: none; position: absolute; right: 16px; bottom: calc(100% + 6px); left: 16px; z-index: 55;
    flex-direction: column; max-height: 300px; overflow: hidden;
    border: 1px solid var(--sm-slate-200); border-radius: 12px; background: #fff;
    box-shadow: 0 12px 36px rgba(15,23,42,.16);
}
.s-modern .si-qr-menu.open { display: flex; }
.s-modern .si-qr-menu-head { display: flex; align-items: center; gap: 8px; padding: 9px 14px; border-bottom: 1px solid var(--sm-slate-100); color: var(--sm-slate-800); font-size: 12px; font-weight: 700; }
.s-modern .si-qr-menu-head i { color: var(--sm-primary); }
.s-modern .si-qr-menu-head small { margin-left: auto; color: var(--sm-slate-500); font-size: 11px; font-weight: 500; }
.s-modern .si-qr-menu-list { flex: 1; min-height: 0; overflow-y: auto; padding: 4px; }
.s-modern .si-qr-item {
    display: flex; align-items: flex-start; gap: 10px; width: 100%; padding: 8px 10px;
    border: 0; border-radius: 8px; background: transparent; font: inherit; text-align: left; cursor: pointer;
}
.s-modern .si-qr-item.active { background: rgba(99,102,241,.08); }
.s-modern .si-qr-item .si-qr-badge { max-width: 140px; }
.s-modern .si-qr-item-text { flex: 1; min-width: 0; color: var(--sm-slate-700); font-size: 12.5px; line-height: 1.4; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-qr-item[hidden],
.s-modern .si-qr-empty[hidden] { display: none; }
.s-modern .si-qr-empty { padding: 14px; color: var(--sm-slate-500); font-size: 12.5px; text-align: center; }
.s-modern .si-qr-manage { flex: none; padding: 9px 14px; border: 0; border-top: 1px solid var(--sm-slate-100); background: var(--sm-slate-50); color: var(--sm-primary); font: inherit; font-size: 12px; font-weight: 600; text-align: left; cursor: pointer; }
.s-modern .si-qr-manage:hover { background: var(--sm-slate-100); }

/* ---------- Media, links & docs (WhatsApp-style, grouped by date) ---------- */
.s-modern .si-media-tabs { flex: none; display: flex; gap: 4px; padding: 6px 16px 0; border-bottom: 1px solid var(--sm-slate-100); background: #fff; }
.s-modern .si-media-tab {
    position: relative; display: inline-flex; align-items: center; gap: 6px; padding: 9px 12px 11px;
    border: 0; background: transparent; color: var(--sm-slate-500); font: inherit; font-size: 13px; font-weight: 600; cursor: pointer;
}
.s-modern .si-media-tab:hover { color: var(--sm-slate-800); }
.s-modern .si-media-tab.active { color: var(--sm-primary); }
.s-modern .si-media-tab.active::after { content: ""; position: absolute; right: 8px; bottom: -1px; left: 8px; height: 3px; border-radius: 3px 3px 0 0; background: var(--sm-primary); }
.s-modern .si-media-tab small { padding: 1px 6px; border-radius: 999px; background: var(--sm-slate-100); color: var(--sm-slate-600); font-size: 10.5px; font-weight: 600; }
.s-modern .si-media-tab.active small { background: rgba(99,102,241,.12); color: var(--sm-primary); }
.s-modern .si-media-body { flex: 1; min-height: 0; overflow-y: auto; padding: 0 20px 24px; background: #fff; }
.s-modern .si-media-date {
    position: sticky; top: 0; z-index: 2; padding: 14px 2px 8px;
    background: rgba(255,255,255,.96); color: var(--sm-slate-600); font-size: 12px; font-weight: 700;
}
.s-modern .si-media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 6px; }
.s-modern .si-media-tile { position: relative; aspect-ratio: 1 / 1; padding: 0; overflow: hidden; border: 0; border-radius: 8px; background: var(--sm-slate-100); cursor: pointer; }
.s-modern .si-media-tile-inner { position: absolute; inset: 0; }
.s-modern .si-media-tile img,
.s-modern .si-media-tile video { display: block; width: 100%; height: 100%; object-fit: cover; transition: transform .2s; }
.s-modern .si-media-tile:hover img,
.s-modern .si-media-tile:hover video { transform: scale(1.04); }
.s-modern .si-media-tile-play {
    position: absolute; top: 50%; left: 50%; width: 36px; height: 36px; transform: translate(-50%, -50%);
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50%; background: rgba(15,23,42,.55); color: #fff; font-size: 12px; pointer-events: none;
}
.s-modern .si-media-tile-dur { position: absolute; top: 6px; right: 6px; padding: 1px 6px; border-radius: 4px; background: rgba(15,23,42,.6); color: #fff; font-size: 10.5px; pointer-events: none; }
.s-modern .si-media-tile-cap {
    position: absolute; right: 0; bottom: 0; left: 0; display: flex; justify-content: space-between; gap: 6px;
    padding: 18px 8px 6px; background: linear-gradient(180deg, transparent, rgba(15,23,42,.75));
    color: #fff; font-size: 11px; text-align: left; opacity: 0; transition: opacity .15s; pointer-events: none;
}
.s-modern .si-media-tile:hover .si-media-tile-cap,
.s-modern .si-media-tile:focus-visible .si-media-tile-cap { opacity: 1; }
.s-modern .si-media-tile-cap span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-media-rows { overflow: hidden; border: 1px solid var(--sm-slate-200); border-radius: 10px; }
.s-modern .si-media-row { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-bottom: 1px solid var(--sm-slate-100); background: #fff; cursor: pointer; transition: background .12s; }
.s-modern .si-media-row:last-child { border-bottom: 0; }
.s-modern .si-media-row:hover { background: var(--sm-slate-50); }
.s-modern .si-media-row-icon { width: 38px; height: 38px; flex: none; display: inline-flex; align-items: center; justify-content: center; border-radius: 9px; font-size: 15px; }
.s-modern .si-media-row-icon.audio { background: #ecfdf5; color: #059669; }
.s-modern .si-media-row-icon.doc { background: #fef2f2; color: #dc2626; }
.s-modern .si-media-row-icon.link { background: #eff6ff; color: #2563eb; }
.s-modern .si-media-row-body { flex: 1; min-width: 0; }
.s-modern .si-media-row-title { color: var(--sm-slate-800); font-size: 13px; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-media-row-title a { color: #2563eb; }
.s-modern .si-media-row-sub { margin-top: 2px; color: var(--sm-slate-500); font-size: 11.5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.s-modern .si-media-row-time { flex: none; color: var(--sm-slate-500); font-size: 11px; }
.s-modern .si-media-row-dl { width: 30px; height: 30px; flex: none; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; color: var(--sm-slate-500); }
.s-modern .si-media-row-dl:hover { background: var(--sm-slate-100); color: var(--sm-primary); }
.s-modern .si-media-more { display: flex; justify-content: center; margin-top: 18px; }
.s-modern .si-media-body::-webkit-scrollbar,
.s-modern .si-pane-body::-webkit-scrollbar { width: 6px; }
.s-modern .si-media-body::-webkit-scrollbar-thumb,
.s-modern .si-pane-body::-webkit-scrollbar-thumb { background: var(--sm-slate-300); border-radius: 3px; }

/* ---------- Lightbox ---------- */
.si-lightbox {
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, 0.88);
    display: none; align-items: center; justify-content: center;
    z-index: 100000; padding: 20px;
}
.si-lightbox.open { display: flex; }
.si-lightbox img { max-width: 100%; max-height: 100%; border-radius: 6px; }
.si-lightbox .close-btn {
    position: absolute; top: 20px; right: 24px;
    background: none; border: none; color: #fff;
    font-size: 32px; cursor: pointer;
}

/* ---------- Photos & videos send preview (my-chat's .img-preview-modal) ----------
   Appended to <body> by siOpenMediaPreview(), so not scoped under .s-modern.
   Sits below the call UI (100040+) so a ringing call still shows on top. */
.si-mpm { position: fixed; inset: 0; z-index: 100003; display: none; flex-direction: column; background: #0a0d0e; }
.si-mpm.open { display: flex; }
body.si-mpm-open #adminSupportCallStatus { display: none !important; }
.si-mpm-topbar {
    flex: 0 0 auto; display: flex; align-items: center; justify-content: space-between;
    padding: calc(14px + env(safe-area-inset-top, 0px)) 18px 14px; color: #fff; background: #15191b;
}
.si-mpm-icon {
    width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
    border: none; border-radius: 50%; background: transparent; color: #fff; cursor: pointer; transition: background 120ms;
}
.si-mpm-icon:hover { background: rgba(255,255,255,0.08); }
.si-mpm-count { color: rgba(255,255,255,0.75); font-size: 14px; font-variant-numeric: tabular-nums; }
.si-mpm-body {
    position: relative; flex: 1 1 auto; min-height: 0; display: flex; align-items: center; justify-content: center;
    padding: 16px; overflow: hidden; background: #0a0d0e;
}
.si-mpm-stage { flex: 1 1 auto; align-self: stretch; min-width: 0; min-height: 0; display: flex; align-items: center; justify-content: center; }
.si-mpm-stage > img,
.si-mpm-stage > video { display: block; max-width: 100%; max-height: 100%; border-radius: 8px; object-fit: contain; background: #000; }
/* Video with a trim bar: stack the player above the bar. */
.si-mpm-body.has-trim { flex-direction: column; gap: 14px; padding-top: 56px; }

/* ---- Trim bar (filmstrip + start/end handles) ---- */
.si-mpm-trim { flex: 0 0 auto; width: min(760px, 100%); align-self: center; user-select: none; -webkit-user-select: none; }
.si-mpm-strip { position: relative; height: 52px; margin: 0 14px; border-radius: 4px; background: #1f2427; touch-action: none; cursor: pointer; }
.si-mpm-frames { position: absolute; inset: 0; display: flex; overflow: hidden; border-radius: 4px; }
.si-mpm-frames img { flex: 1 1 0; min-width: 0; height: 100%; object-fit: cover; }
.si-mpm-shade { position: absolute; top: 0; bottom: 0; background: rgba(6,8,9,.68); pointer-events: none; }
.si-mpm-shade.start { left: 0; border-radius: 4px 0 0 4px; }
.si-mpm-shade.end { right: 0; border-radius: 0 4px 4px 0; }
.si-mpm-window { position: absolute; top: -3px; bottom: -3px; border-top: 3px solid #C1F11D; border-bottom: 3px solid #C1F11D; pointer-events: none; }
.si-mpm-playhead {
    position: absolute; top: -6px; bottom: -6px; width: 3px; margin-left: -1.5px;
    border-radius: 2px; background: #fff; box-shadow: 0 0 4px rgba(0,0,0,.6); pointer-events: none;
}
.si-mpm-handle {
    position: absolute; top: -3px; bottom: -3px; width: 16px; padding: 0;
    border: 0; background: #C1F11D; cursor: ew-resize; touch-action: none;
}
.si-mpm-handle.start { margin-left: -16px; border-radius: 6px 0 0 6px; }
.si-mpm-handle.end { border-radius: 0 6px 6px 0; }
/* Grip line in the middle of each handle. */
.si-mpm-handle::after {
    content: ""; position: absolute; top: 50%; left: 50%; width: 3px; height: 18px;
    border-radius: 2px; background: #0a0d0e; transform: translate(-50%, -50%);
}
.si-mpm-handle:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }
.si-mpm-handle.dragging { background: #d9ff4a; }
.si-mpm-trim-times {
    display: flex; align-items: center; justify-content: space-between; margin: 8px 14px 0;
    color: rgba(255,255,255,0.6); font-size: 12px; font-variant-numeric: tabular-nums;
}
.si-mpm-trim-times strong { color: #fff; font-size: 13px; font-weight: 600; }
/* In-browser trimming progress — covers the whole popup while recording. */
.si-mpm-busy { position: absolute; inset: 0; z-index: 5; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(6,8,9,0.78); }
.si-mpm-busy[hidden] { display: none; }
.si-mpm-busy-card { width: min(380px, 100%); padding: 20px; border-radius: 14px; background: #15191b; color: #fff; text-align: center; box-shadow: 0 16px 40px rgba(0,0,0,.5); }
.si-mpm-busy-title { font-size: 15px; font-weight: 600; }
.si-mpm-busy-bar { height: 6px; margin: 14px 0 10px; overflow: hidden; border-radius: 99px; background: rgba(255,255,255,0.12); }
.si-mpm-busy-bar span { display: block; width: 0; height: 100%; border-radius: inherit; background: #C1F11D; transition: width .15s linear; }
.si-mpm-busy-note { color: rgba(255,255,255,0.65); font-size: 12.5px; line-height: 1.45; }
.si-mpm-busy-btn {
    margin-top: 14px; padding: 8px 18px; border: 1px solid rgba(255,255,255,0.18); border-radius: 999px;
    background: transparent; color: #fff; font: inherit; font-size: 13px; font-weight: 600; cursor: pointer;
}
.si-mpm-busy-btn:hover { background: rgba(255,255,255,0.08); }
.si-mpm-busy.is-error .si-mpm-busy-bar { display: none; }
.si-mpm-busy.is-error .si-mpm-busy-title { color: #fca5a5; }
.si-mpm-trim-off {
    position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);
    width: max-content; max-width: calc(100% - 32px); padding: 7px 12px; border-radius: 8px;
    background: rgba(239,68,68,0.16); color: #fca5a5; font-size: 12px; line-height: 1.4; text-align: center;
    overflow-wrap: anywhere;
}
.si-mpm-meta {
    position: absolute; top: 14px; left: 50%; transform: translateX(-50%);
    display: inline-flex; align-items: center; gap: 7px; padding: 5px 12px; border-radius: 999px;
    background: rgba(21,25,27,0.85); color: rgba(255,255,255,0.85); font-size: 12.5px; font-variant-numeric: tabular-nums;
    pointer-events: none;
}
.si-mpm-thumbs {
    flex: 0 0 auto; display: none; align-items: center; gap: 6px; padding: 8px 12px; overflow-x: auto;
    border-top: 1px solid rgba(255,255,255,0.06); background: #15191b; scrollbar-width: thin;
}
.si-mpm-thumbs.open { display: flex; }
.si-mpm-thumbs::-webkit-scrollbar { height: 4px; }
.si-mpm-thumbs::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }
.si-mpm-thumb {
    position: relative; flex: 0 0 auto; width: 52px; height: 52px; padding: 0;
    border: 2px solid transparent; border-radius: 6px; background: #000 center / cover no-repeat;
    cursor: pointer; transition: border-color 120ms, transform 120ms;
}
.si-mpm-thumb:hover { transform: scale(1.05); }
.si-mpm-thumb.active { border-color: #C1F11D; }
/* Small play mark so videos read as videos in the strip. */
.si-mpm-thumb.is-video::after {
    content: ""; position: absolute; bottom: 4px; left: 5px;
    border-top: 5px solid transparent; border-bottom: 5px solid transparent; border-left: 8px solid #fff;
    filter: drop-shadow(0 0 2px rgba(0,0,0,.8));
}
.si-mpm-thumb-add {
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.65); font-size: 22px; font-weight: 300; line-height: 1;
}
.si-mpm-thumb-add:hover { background: rgba(255,255,255,0.16); color: #fff; }
.si-mpm-footer {
    flex: 0 0 auto; display: flex; align-items: center; gap: 10px;
    padding: 12px 14px calc(12px + env(safe-area-inset-bottom, 0px));
    border-top: 1px solid rgba(255,255,255,0.06); background: #15191b;
}
.si-mpm-caption {
    flex: 1 1 auto; min-width: 0; padding: 12px 16px;
    border: 1px solid rgba(255,255,255,0.08); border-radius: 999px; outline: none;
    background: #0a0d0e; color: #fff; font-family: inherit; font-size: 14px;
}
.si-mpm-caption::placeholder { color: rgba(255,255,255,0.4); }
.si-mpm-caption:focus { border-color: rgba(193,241,29,0.4); }
.si-mpm-send {
    flex: 0 0 auto; width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center;
    border: none; border-radius: 50%; background: #22c55e; color: #fff; cursor: pointer;
    transition: background 120ms, transform 120ms;
}
.si-mpm-send:hover { background: #16a34a; transform: scale(1.05); }

/* ---------- Phone-only pieces: hidden on desktop ---------- */
.s-modern .si-time.is-unread { color: var(--sm-primary); font-weight: 600; }
.s-modern .si-menu-btn,
.s-modern .si-bottom-tab-label,
.s-modern .si-wrap .si-mobile-only { display: none; }

/* ---------- Phones: WhatsApp-style single pane ----------
   The inbox becomes a full-screen app: the list (with a bottom tab bar), or
   the opened chat / pane with a back arrow. .is-detail on .si-wrap is set by
   the server render and flipped early by the JS for an instant feel. */
@media (max-width: 767px) {
    body { overflow: hidden; }
    .s-modern > .page-title { display: none; }
    /* The shell covers the admin chrome; the list header carries the menu button instead. */
    #mm-btn { display: none !important; }
    /* Call-listener pill: hidden while live, otherwise a centred "Connecting…" chip above the bars. */
    #adminSupportCallStatus {
        left: 50% !important; bottom: calc(76px + env(safe-area-inset-bottom, 0px)) !important;
        transform: translateX(-50%);
    }
    #adminSupportCallStatus[data-state="live"] { display: none !important; }
    .s-modern > .si-broadcast-notice {
        position: fixed; top: calc(70px + env(safe-area-inset-top, 0px)); right: 12px; left: 12px;
        z-index: 1050; box-shadow: 0 8px 24px rgba(15, 23, 42, .18);
    }

    /* iOS Safari zooms into any field under 16px and never zooms back out. */
    .s-modern input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
    .s-modern select,
    .s-modern textarea,
    .si-stp-card input,
    .si-mpm-caption { font-size: 16px !important; }
    .s-modern .si-status-card textarea { font-size: inherit !important; }

    .s-modern .si-wrap {
        position: fixed; inset: 0; z-index: 1040;
        height: auto; min-height: 0; gap: 0; background: #fff;
    }
    /* siTrackViewport() follows the visual viewport so the composer rides
       above the on-screen keyboard instead of hiding behind it. */
    html.si-vv .s-modern .si-wrap { top: var(--si-vv-top); bottom: auto; height: var(--si-vv-height); }
    .s-modern .si-side,
    .s-modern .si-main {
        width: 100%; min-width: 0; flex: 1 1 auto;
        border: 0; border-radius: 0; box-shadow: none;
    }
    .s-modern .si-main { display: none; }
    .s-modern .si-wrap.is-detail .si-side { display: none; }
    .s-modern .si-wrap.is-detail .si-main { display: flex; }
    .s-modern .si-wrap .si-pane-back.si-mobile-only { display: inline-flex; }
    .s-modern .si-wrap .si-desktop-only { display: none; }
    /* Tapped a row: spinner over the pane until the server render lands. */
    .s-modern .si-wrap.is-opening .si-main::after {
        content: ""; position: absolute; inset: 0; z-index: 80;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 50 50'%3E%3Ccircle cx='25' cy='25' r='20' fill='none' stroke='%236366f1' stroke-width='4' stroke-linecap='round' stroke-dasharray='90 40'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='0.8s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/svg%3E") center / 34px no-repeat;
    }
    .s-modern .si-list,
    .s-modern .si-msgs,
    .s-modern .si-media-body,
    .s-modern .si-pane-body,
    .s-modern .si-starred-body { overscroll-behavior: contain; -webkit-overflow-scrolling: touch; }

    /* ----- List header ----- */
    .s-modern .si-side-head { padding: calc(8px + env(safe-area-inset-top, 0px)) 12px 10px 6px; border-bottom: 0; }
    .s-modern .si-side-title { font-size: 21px !important; gap: 4px !important; letter-spacing: -.01em; }
    .s-modern .si-side-icon { display: none; }
    .s-modern .si-menu-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 42px; height: 42px; flex: none; padding: 0;
        border: 0; border-radius: 50%; background: transparent;
        color: var(--sm-slate-700); font-size: 19px; cursor: pointer;
    }
    .s-modern .si-menu-btn:active { background: var(--sm-slate-100); }
    .s-modern .si-side-head > div:first-child > .si-badge { margin-right: 4px; }
    .s-modern .si-side-head .si-search,
    .s-modern .si-side-head .si-calls-paused { margin-left: 6px; width: calc(100% - 6px); }
    .s-modern .si-side-head .si-chat-filters { margin-left: 6px; }
    .s-modern .si-chat-filter { padding: 6px 13px; font-size: 13.5px; }
    .s-modern .si-search {
        height: 42px; padding: 0 16px 0 42px;
        border: 0; border-radius: 999px;
        background: var(--sm-slate-100) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.2' stroke-linecap='round'%3E%3Ccircle cx='11' cy='11' r='7'/%3E%3Cpath d='m20 20-3.5-3.5'/%3E%3C/svg%3E") 15px center / 17px no-repeat;
    }
    .s-modern .si-search:focus { background-color: #fff; box-shadow: 0 0 0 2px var(--sm-primary); }

    /* ----- List rows ----- */
    .s-modern .si-list { padding: 0 0 12px; }
    .s-modern .si-item { position: relative; margin: 0; padding: 10px 16px; border-radius: 0; }
    .s-modern .si-item::after {
        content: ""; position: absolute; right: 0; bottom: 0; left: 80px;
        height: 1px; background: var(--sm-slate-100);
    }
    .s-modern .si-item:last-child::after { display: none; }
    .s-modern .si-item.active { background: transparent; box-shadow: none; }
    .s-modern .si-item:active,
    .s-modern .si-set-row:active,
    .s-modern .si-status-me:active { background: var(--sm-slate-100); }
    .s-modern .si-item .si-avatar,
    .s-modern .si-item .si-status-thumb { width: 50px; height: 50px; margin-right: 14px; font-size: 18px; }
    .s-modern .si-item .si-avatar-wrap { margin-right: 14px; }
    .s-modern .si-online-dot { right: 1px; bottom: 1px; width: 13px; height: 13px; }
    .s-modern .si-name { font-size: 16px; }
    .s-modern .si-country-prefix { font-size: 11px; }
    .s-modern .si-preview { margin-top: 2px; font-size: 14px; }
    .s-modern .si-time { margin-bottom: 6px; font-size: 12px; }
    .s-modern .si-meta .si-badge,
    .s-modern .si-meta .si-view-tab-badge {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 21px; height: 21px; padding: 0 6px; font-size: 11.5px;
    }
    .s-modern .si-row-actions-trigger { width: 30px; height: 28px; font-size: 13px; }
    .s-modern .si-row-actions-menu button { padding: 11px 10px; font-size: 14px; }
    .s-modern .si-status-me,
    .s-modern .si-set-profile { padding: 10px 10px 14px; }
    .s-modern .si-status-actions { padding: 0 10px 12px; }
    .s-modern .si-set-row { margin: 0; padding: 13px 14px; border-radius: 0; }
    .s-modern .si-set-row.active { background: transparent; box-shadow: none; }
    .s-modern .si-set-row.active .si-set-row-icon { background: var(--sm-slate-100); color: var(--sm-slate-600); }
    .s-modern .si-section-label,
    .s-modern .si-status-hint { padding-right: 16px; padding-left: 16px; }

    /* ----- Bottom tab bar (icon in a pill + label) ----- */
    .s-modern .si-bottom-tabs {
        padding: 4px 2px calc(4px + env(safe-area-inset-bottom, 0px));
        border-top: 1px solid var(--sm-slate-200); background: #fff;
    }
    .s-modern .si-bottom-tab,
    .s-modern .si-bottom-tab:hover,
    .s-modern .si-bottom-tab.active {
        flex: 1 1 0; width: auto; height: auto; min-width: 0;
        flex-direction: column; gap: 5px; padding: 0 0 4px;
        border-radius: 0; background: transparent; box-shadow: none;
    }
    .s-modern .si-bottom-tab { color: var(--sm-slate-500); }
    .s-modern .si-bottom-tab.active { color: var(--sm-primary); }
    .s-modern .si-bottom-tab::before {
        content: ""; position: absolute; top: 3px; left: 50%;
        width: 56px; height: 30px; margin-left: -28px;
        border-radius: 999px; background: transparent; transition: background .15s;
    }
    .s-modern .si-bottom-tab.active::before { background: rgba(99, 102, 241, .13); }
    .s-modern .si-bottom-tab svg { position: relative; width: 20px; height: 20px; margin-top: 8px; }
    .s-modern .si-bottom-tab-label { display: block; font-size: 11.5px; font-weight: 500; line-height: 1.2; }
    .s-modern .si-bottom-tab.active .si-bottom-tab-label { font-weight: 700; }
    .s-modern .si-tab-badge { top: 0; right: auto; left: calc(50% + 6px); box-shadow: 0 0 0 2px #fff; }
    .s-modern .si-tab-dot { top: 5px; right: auto; left: calc(50% + 10px); box-shadow: 0 0 0 2px #fff; }

    /* ----- Shared pane header (chat, media, status, settings, starred) ----- */
    .s-modern .si-head,
    .s-modern .si-pane-head,
    .s-modern .si-starred-head,
    .s-modern .si-info-head {
        gap: 6px; padding: calc(8px + env(safe-area-inset-top, 0px)) 8px 8px 4px;
        background: #fff;
    }
    /* Contact info: same page, sized for a phone. */
    .s-modern .si-info-panel { animation: none; }
    .s-modern .si-info-close { width: 40px; height: 40px; font-size: 17px; }
    .s-modern .si-info-body { overscroll-behavior: contain; -webkit-overflow-scrolling: touch; }
    .s-modern .si-info-hero { padding: 26px 16px 22px; }
    .s-modern .si-info-avatar { width: 128px; height: 128px; font-size: 50px; }
    .s-modern .si-info-name { font-size: 23px; }
    .s-modern .si-info-actions { gap: 20px; margin-top: 22px; }
    .s-modern .si-info-action-icon { width: 54px; height: 54px; font-size: 19px; }
    .s-modern .si-info-row { padding: 13px 16px; gap: 14px; }
    .s-modern .si-info-section { padding: 16px; }
    .s-modern .si-pane-back,
    .s-modern .si-starred-back { width: 40px; height: 40px; font-size: 17px; }
    .s-modern .si-pane-title { font-size: 16px; }
    .s-modern .si-pane-sub { overflow: hidden; font-size: 12.5px; text-overflow: ellipsis; white-space: nowrap; }
    .s-modern .si-pane-head > div:not(.si-pane-head-actions) { flex: 1; min-width: 0; }

    /* ----- Chat thread ----- */
    .s-modern .si-head-who { flex: 1; min-width: 0; }
    .s-modern .si-head-profile > .si-avatar { width: 40px !important; height: 40px !important; flex: none; }
    .s-modern .si-head-profile:hover { background: transparent; }
    .s-modern .si-head-profile:active { background: var(--sm-slate-100); }
    .s-modern .si-head-text { flex: 1; min-width: 0; }
    .s-modern .si-head-name,
    .s-modern .si-head-mail { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .s-modern .si-head-name { font-size: 16px; }
    .s-modern .si-head-name .si-country-prefix { display: none; }
    .s-modern .si-head-actions { flex: none; gap: 0; }
    .s-modern .si-hbtn,
    .s-modern .si-hbtn:hover {
        width: 40px; height: 40px; border: 0; background: transparent;
        color: var(--sm-slate-700); font-size: 17px; transform: none;
    }
    .s-modern .si-hbtn:active { background: var(--sm-slate-100); }
    /* Bigger touch targets for the ⋮ menu. */
    .s-modern .si-head-more-menu { top: 44px; min-width: 210px; padding: 6px; border-radius: 12px; }
    .s-modern .si-head-more-menu button { gap: 12px; padding: 12px; border-radius: 8px; font-size: 15px; }
    .s-modern .si-head-more-menu button:active { background: var(--sm-slate-100); }
    .s-modern .si-head-more-menu button i { width: 18px; }
    .s-modern .si-head-flag { padding: 7px 14px; }

    .s-modern .si-msgs { padding: 10px 16px 14px; }
    .s-modern .si-typing-row { padding: 0 16px 10px; }
    .s-modern .si-bub { max-width: 85%; font-size: 15px; line-height: 20px; }
    .s-modern .si-msg-image { max-width: min(260px, 68vw); }
    .s-modern .si-msg.media video { max-width: min(260px, 68vw) !important; }
    .s-modern .si-msg-voice { min-width: 0; width: min(300px, 72vw); }
    .s-modern .si-msg-file { min-width: 0; }
    .s-modern .si-call { min-width: 0; }
    .s-modern .si-wallet-receipt { min-width: 0; }

    /* ----- Composer: pill input, round mic/send button ----- */
    .s-modern .si-input {
        padding: 6px 8px calc(6px + env(safe-area-inset-bottom, 0px));
        border-top: 0; background: var(--sm-slate-100);
    }
    .s-modern .si-input form { gap: 4px !important; }
    .s-modern .si-input textarea {
        min-height: 44px; padding: 11px 16px;
        border: 0; border-radius: 22px; background: #fff; line-height: 1.35;
    }
    .s-modern .si-input textarea:focus { background: #fff; box-shadow: none; }
    .s-modern .si-input .si-att-btn,
    .s-modern .si-input .si-suggest-btn { width: 40px; height: 40px; flex: none; font-size: 18px; }
    .s-modern .si-input .si-att-btn { color: var(--sm-slate-600); }
    .s-modern .si-input .si-mic-btn,
    .s-modern .si-input .si-mic-btn:hover,
    .s-modern .si-input button[type="submit"] {
        width: 46px; height: 46px; flex: none; border-radius: 50%; font-size: 17px;
        background: linear-gradient(180deg, var(--sm-primary) 0%, var(--sm-primary-dark) 100%);
        color: #fff; box-shadow: 0 3px 8px rgba(99, 102, 241, .25);
    }
    .s-modern .si-input .si-mic-btn.recording { background: var(--sm-danger); }
    /* WhatsApp swap: mic while the box is empty, send once there's text. */
    .s-modern .si-input textarea:placeholder-shown ~ button[type="submit"],
    .s-modern .si-input textarea:not(:placeholder-shown) ~ .si-mic-btn { display: none; }
    .s-modern .si-attach-menu { bottom: 50px; min-width: 220px; }
    .s-modern .si-attach-menu button { padding: 12px; font-size: 15px; }
    .s-modern .si-qr-menu { right: 8px; left: 8px; max-height: 50vh; }
    .s-modern .si-qr-menu-head small { display: none; }
    .s-modern .si-suggest-menu { right: -50px; }
    .s-modern .si-suggest-chip { margin: 0 8px 8px; }
    .s-modern .si-att-preview,
    .s-modern .si-reply-composer { padding-right: 12px; padding-left: 12px; }

    /* ----- Media ----- */
    .s-modern .si-media-tabs { padding: 2px 8px 0; overflow-x: auto; scrollbar-width: none; }
    .s-modern .si-media-tabs::-webkit-scrollbar { display: none; }
    .s-modern .si-media-tab { flex: none; padding: 10px 10px 12px; }
    .s-modern .si-media-body { padding: 0 6px 16px; }
    .s-modern .si-media-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 3px; }
    .s-modern .si-media-tile { border-radius: 4px; }
    .s-modern .si-media-row { padding: 12px; }

    /* ----- Status ----- */
    .s-modern .si-status-body { flex-direction: column; }
    .s-modern .si-status-stage { flex: 1 1 auto; padding: 16px; }
    .s-modern .si-status-stage img,
    .s-modern .si-status-stage video { max-height: 52vh; }
    .s-modern .si-status-card { width: min(300px, 100%); }
    .s-modern .si-status-viewers { width: auto; max-height: 38%; border-top: 1px solid var(--sm-slate-100); border-left: 0; }

    /* ----- Settings ----- */
    .s-modern .si-pane-body { padding: 16px 14px calc(24px + env(safe-area-inset-bottom, 0px)); }
    .s-modern .si-set-card { gap: 12px; padding: 14px; }
    .s-modern .si-blocked-row { flex-wrap: wrap; }
    .s-modern .si-qr-badge { max-width: 110px; }

    /* ----- Dialogs become bottom sheets ----- */
    .s-modern .si-modal { align-items: flex-end; padding: 0; }
    .s-modern .si-modal-card,
    .s-modern .si-modal-card.si-broadcast-card {
        width: 100% !important; max-width: none !important; max-height: 92vh; max-height: 92dvh;
        overflow-y: auto; overscroll-behavior: contain;
        padding: 20px 16px calc(18px + env(safe-area-inset-bottom, 0px));
        border-radius: 18px 18px 0 0;
    }
    .s-modern .si-modal-actions button { padding: 11px 18px; font-size: 14px; }
}
</style>
@endpush

<div class="s-modern">
    <div class="row page-title clearfix">
        <div class="page-title-left">
            <h5 class="mr-0 mr-r-5">Support Inbox</h5>
            <p class="mr-0 text-muted d-none d-md-inline-block">Reply to customer support chats in real time</p>
        </div>
        <div class="page-title-right d-inline-flex align-items-center" style="gap:12px;">
            <ol class="breadcrumb d-none d-md-flex">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Support Inbox</li>
            </ol>
        </div>
    </div>

    @if(session()->has('broadcast-success'))
        <div class="si-broadcast-notice" role="status">{{ session('broadcast-success') }}</div>
    @endif

    @php
        // Phones show one pane at a time, WhatsApp-style: the list, or — once
        // something is opened from it — the right-hand pane with a back arrow.
        $siDetailOpen = $starredViewOpen || match ($inboxTab) {
            'media' => $mediaPaneOpen,
            'status' => $statusComposerOpen || $selectedStatus,
            'settings' => $settingsSection !== '',
            default => $selectedConversationId && $selectedCustomer,
        };
    @endphp
    <div class="si-wrap {{ $siDetailOpen ? 'is-detail' : '' }}" data-inbox-rev="{{ $inboxRev }}">
        {{-- Left: conversation list --}}
        <div class="si-side">
            @php
                [$sideIcon, $sideTitle] = match ($inboxTab) {
                    'calls' => ['fa-phone', 'Recent calls'],
                    'status' => ['fa-circle-notch', 'Status'],
                    'media' => ['fa-images', 'Media'],
                    'settings' => ['fa-cog', 'Settings'],
                    default => ['fa-headset', 'Customers'],
                };
                $mediaLabels = ['media' => 'Media', 'audio' => 'Audio', 'docs' => 'Docs', 'links' => 'Links'];
            @endphp
            <div class="si-side-head">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:{{ in_array($inboxTab, ['status', 'settings'], true) ? '0' : '8px' }};">
                    <div class="si-side-title" style="color: var(--sm-slate-800); font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 6px;">
                        {{-- Phones only: the inbox covers the admin chrome, so it carries the menu button. --}}
                        <button type="button" class="si-menu-btn" onclick="document.getElementById('mm-btn')?.click()" aria-label="Open admin menu" title="Menu">
                            <i class="fa fa-bars"></i>
                        </button>
                        <i class="fa {{ $sideIcon }} si-side-icon" style="color: var(--sm-primary);"></i>
                        {{ $sideTitle }}
                        {{-- wire:ignore: siInit() flips offline→online client-side; without it every re-render morphs the class back to offline. --}}
                        <span id="si-conn" class="si-conn offline" wire:ignore><i class="fa fa-circle"></i></span>
                    </div>
                    
                </div>
                @if($supportCallsBlocked && in_array($inboxTab, ['chat', 'calls', 'media'], true))
                    <button type="button" class="si-calls-paused" wire:click="setSettingsSection('calls')">
                        <i class="fa fa-phone-slash"></i>
                        <span>All support calls are paused</span>
                        <strong>Manage</strong>
                    </button>
                @endif
                @if(in_array($inboxTab, ['chat', 'calls', 'media'], true))
                    <input type="text" class="si-search" placeholder="{{ $inboxTab === 'media' ? 'Search customers…' : 'Search by name or email…' }}" wire:model.live.debounce.300ms="searchTerm">
                @endif
                @if($inboxTab === 'chat')
                    <div class="si-chat-filters" role="tablist" aria-label="Filter chats">
                        @foreach(['all' => 'All', 'unread' => 'Unread', 'archived' => 'Archived'] as $filterKey => $filterLabel)
                            @php $filterCount = ['unread' => $unreadChatCount, 'archived' => $archivedChatCount][$filterKey] ?? 0; @endphp
                            <button type="button" class="si-chat-filter {{ $chatFilter === $filterKey ? 'active' : '' }}"
                                    wire:click="setChatFilter('{{ $filterKey }}')" role="tab" aria-selected="{{ $chatFilter === $filterKey ? 'true' : 'false' }}">
                                @if($filterKey === 'archived')<i class="fa fa-archive"></i>@endif
                                {{ $filterLabel }}
                                @if($filterCount > 0)<small>{{ $filterCount }}</small>@endif
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
            @php
                // List times as WhatsApp shows them: the time today, then
                // "Yesterday", the weekday within a week, then the date.
                $siListTime = function ($at) {
                    $at = \Carbon\Carbon::parse($at);
                    if ($at->isToday()) return $at->format('h:i A');
                    if ($at->isYesterday()) return 'Yesterday';
                    if ($at->gte(now()->subDays(6)->startOfDay())) return $at->format('l');
                    return $at->format('d/m/Y');
                };
            @endphp
            <div class="si-list">
                @if($inboxTab === 'chat')
                    @forelse($conversations as $c)
                        <div class="si-item {{ $selectedConversationId == $c['id'] ? 'active' : '' }}"
                             wire:click="selectConversation({{ $c['id'] }})" data-si-open
                             wire:key="si-{{ $c['id'] }}">
                            <div class="si-avatar-wrap">
                                <div class="si-avatar" style="--si-av: {{ user_avatar_color($c['customer_id'] ?? $c['customer_name']) }}">
                                    {{ strtoupper(substr($c['customer_name'] ?? '?', 0, 1)) }}
                                    @if(!empty($c['customer_avatar']))
                                        <img src="{{ $c['customer_avatar'] }}" alt="" wire:key="si-av-{{ md5($c['customer_avatar']) }}" wire:ignore onerror="this.style.display='none'">
                                    @endif
                                </div>
                                @if(!empty($c['is_online']))
                                    <span class="si-online-dot" title="Online" aria-label="Online"></span>
                                @endif
                            </div>
                            <div class="si-info">
                                <div class="si-name">
                                    {{ $c['customer_name'] }}
                                    @if(!empty($c['customer_country']))<span class="si-country-prefix">{{ $c['customer_country'] }}</span>@endif
                                    @if(!empty($c['calls_blocked']))
                                        <i class="fa fa-phone-slash si-flag" title="This customer's calls are blocked"></i>
                                    @endif
                                    {{-- Only a search under All mixes archived chats in. --}}
                                    @if($c['is_archived'] && $chatFilter !== 'archived')
                                        <span class="si-archived-tag"><i class="fa fa-archive"></i> Archived</span>
                                    @endif
                                </div>
                                @php $pv = $c['preview']; @endphp
                                <div class="si-preview {{ $c['unread_count'] > 0 ? 'is-unread' : '' }}">
                                    @if(!$pv)
                                        <span class="si-preview-empty">No messages yet</span>
                                    @else
                                        @if($pv['status'])@include('livewire.partials._support-ticks', ['status' => $pv['status']])@endif
                                        @if($pv['icon'])<i class="fa {{ $pv['icon'] }} si-preview-icon {{ $pv['missed'] ? 'is-missed' : '' }}"></i>@endif
                                        {{ \Illuminate\Support\Str::limit($pv['text'], 60) }}
                                    @endif
                                </div>
                            </div>
                            <div class="si-meta">
                                @if($c['last_message_at'])
                                    <div class="si-time {{ $c['unread_count'] > 0 ? 'is-unread' : '' }}">{{ $siListTime($c['last_message_at']) }}</div>
                                @endif
                                <div class="si-meta-actions">
                                    @if($c['unread_count'] > 0)
                                        <span class="si-badge">{{ $c['unread_count'] }}</span>
                                    @endif
                                    <div class="si-row-actions" onclick="siRowMenu(event, this)">
                                        <button type="button" class="si-row-actions-trigger" title="More options" aria-label="More options for {{ $c['customer_name'] }}">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                        <div class="si-row-actions-menu">
                                            @if($c['is_archived'])
                                                <button type="button" wire:click="unarchiveConversation({{ $c['id'] }})">
                                                    <i class="fa fa-inbox"></i> Unarchive chat
                                                </button>
                                            @else
                                                <button type="button" wire:click="archiveConversation({{ $c['id'] }})"
                                                        title="Moves the chat to Archived. It comes back to All when the customer writes again.">
                                                    <i class="fa fa-archive"></i> Archive chat
                                                </button>
                                            @endif
                                            <button type="button" class="danger" wire:click="hideConversation({{ $c['id'] }})"
                                                    wire:confirm="Delete the chat with {{ $c['customer_name'] }} from the Support Inbox? The customer keeps their messages, and the chat comes back here if they write again.">
                                                <i class="fa fa-trash"></i> Delete chat
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="si-empty" style="padding:40px 20px;">
                            @if($searchTerm !== '')
                                <i class="fa fa-search"></i>
                                <h4>No chats found</h4>
                                <p>No {{ $chatFilter === 'all' ? '' : $chatFilter . ' ' }}chats match “{{ $searchTerm }}”.</p>
                            @elseif($chatFilter === 'unread')
                                <i class="fa fa-check-circle"></i>
                                <h4>No unread chats</h4>
                                <p>You're all caught up.</p>
                            @elseif($chatFilter === 'archived')
                                <i class="fa fa-archive"></i>
                                <h4>No archived chats</h4>
                                <p>Archive a chat from its <i class="fa fa-chevron-down"></i> menu. It comes back to All when the customer writes again.</p>
                            @else
                                <i class="fa fa-inbox"></i>
                                <h4>No support conversations yet</h4>
                                <p>They'll appear here when customers message support.</p>
                            @endif
                        </div>
                    @endforelse
                @elseif($inboxTab === 'calls')
                    @forelse($supportCalls as $call)
                        @php
                            $callIcon = $call['type'] === 'video' ? 'fa-video' : 'fa-phone';
                            $callTitle = match ($call['status']) {
                                'missed' => 'Missed call',
                                'declined' => 'Declined call',
                                'ringing' => 'Ringing',
                                'answered', 'ended' => 'Connected call',
                                default => ucfirst($call['status']) . ' call',
                            };
                        @endphp
                        <div class="si-item {{ $selectedCallId === $call['id'] ? 'active' : '' }} {{ $call['is_unanswered'] ? 'si-call-row-unanswered' : '' }}"
                             wire:click="openCall({{ $call['id'] }})" data-si-open
                             wire:key="si-call-row-{{ $call['id'] }}">
                            <div class="si-avatar" style="--si-av: {{ user_avatar_color($call['customer_id'] ?? $call['customer_name']) }}">
                                {{ strtoupper(substr($call['customer_name'], 0, 1)) }}
                                @if($call['customer_avatar'])
                                    <img src="{{ $call['customer_avatar'] }}" alt="" wire:key="si-av-{{ md5($call['customer_avatar']) }}" wire:ignore onerror="this.style.display='none'">
                                @endif
                            </div>
                            <div class="si-info">
                                <div class="si-name">{{ $call['customer_name'] }}</div>
                                <div class="si-preview si-call-row-status">
                                    <i class="fa {{ $callIcon }}"></i>
                                    {{ $callTitle }} · {{ $call['direction'] }}
                                    @if($call['duration']) · {{ gmdate('i:s', (int) $call['duration']) }} @endif
                                </div>
                            </div>
                            <div class="si-meta">
                                <div class="si-time">{{ $siListTime($call['started_at']) }}</div>
                                <div class="si-meta-actions">
                                    @if($call['is_new'])<span class="si-view-tab-badge">!</span>@endif
                                    <div class="si-row-actions" onclick="siRowMenu(event, this)">
                                        <button type="button" class="si-row-actions-trigger" title="More options" aria-label="More options for this call">
                                            <i class="fa fa-chevron-down"></i>
                                        </button>
                                        <div class="si-row-actions-menu">
                                            <button type="button" class="danger" wire:click="hideCall({{ $call['id'] }})"
                                                    wire:confirm="Delete this call from the Support Inbox call log? The customer's call history is not affected.">
                                                <i class="fa fa-trash"></i> Delete call
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="si-empty" style="padding:40px 20px;">
                            <i class="fa fa-phone"></i>
                            <h4>No support calls yet</h4>
                            <p>Calls made by customers and admins will appear here.</p>
                        </div>
                    @endforelse
                @elseif($inboxTab === 'status')
                    @php $me = auth()->user(); @endphp
                    <button type="button" class="si-status-me" data-si-open
                            @if($myStatuses->isNotEmpty()) wire:click="viewStatus({{ $myStatuses->first()->id }})" @else wire:click="openStatusComposer" @endif>
                        <span class="si-status-me-avatar {{ $myStatuses->isNotEmpty() ? 'has-status' : '' }}">
                            <span class="si-avatar">
                                @if($meAvatarUrl)
                                    <img src="{{ $meAvatarUrl }}" alt="">
                                @else
                                    {{ strtoupper(substr($me->name ?: $me->email, 0, 1)) }}
                                @endif
                            </span>
                            <span class="si-status-me-plus"><i class="fa fa-plus"></i></span>
                        </span>
                        <span class="si-info">
                            <span class="si-name" style="display:block;">My status</span>
                            <span class="si-preview" style="display:block;">
                                @if($myStatuses->isNotEmpty())
                                    {{ $myStatuses->count() }} {{ \Illuminate\Support\Str::plural('update', $myStatuses->count()) }} · {{ $myStatuses->first()->created_at->diffForHumans() }}
                                @else
                                    Share an update with customers
                                @endif
                            </span>
                        </span>
                    </button>
                    <div class="si-status-actions">
                        <button type="button" class="si-set-btn" data-si-status-pick><i class="fa fa-image"></i> Photo / video</button>
                        <button type="button" class="si-set-btn" wire:click="openStatusComposer" data-si-open><i class="fa fa-font"></i> Text</button>
                    </div>
                    @if($myStatuses->isNotEmpty())
                        <div class="si-section-label">Your updates</div>
                        @foreach($myStatuses as $status)
                            <div class="si-item {{ $selectedStatusId === $status->id ? 'active' : '' }}"
                                 wire:click="viewStatus({{ $status->id }})" data-si-open wire:key="si-status-{{ $status->id }}">
                                <div class="si-status-thumb" @if($status->type === 'text') style="background: {{ $status->background_color ?: '#075E54' }};" @endif>
                                    @if($status->type === 'photo')
                                        <img src="{{ route('status.media', $status->id) }}" alt="">
                                    @elseif($status->type === 'video')
                                        <video src="{{ route('status.media', $status->id) }}" preload="metadata" muted></video>
                                        <span class="si-status-thumb-play"><i class="fa fa-play"></i></span>
                                    @else
                                        <i class="fa fa-font"></i>
                                    @endif
                                </div>
                                <div class="si-info">
                                    <div class="si-name">{{ ucfirst($status->type) }}</div>
                                    <div class="si-preview">{{ $status->content ? \Illuminate\Support\Str::limit($status->content, 40) : 'No caption' }}</div>
                                </div>
                                <div class="si-meta">
                                    <div class="si-time">{{ $status->created_at->format('h:i A') }}</div>
                                    <span class="si-status-views" title="Views"><i class="fa fa-eye"></i> {{ $status->views_count }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="si-status-hint">
                        Updates disappear after 24 hours. Customers see them in their Status tab under your name and profile photo.
                    </div>
                @elseif($inboxTab === 'media')
                    {{-- Customer filter for the media pane on the right. --}}
                    <div class="si-item {{ $mediaConversationId === null ? 'active' : '' }}"
                         wire:click="setMediaConversation" data-si-open wire:key="si-media-all">
                        <div class="si-avatar"><i class="fa fa-images"></i></div>
                        <div class="si-info">
                            <div class="si-name">All customers</div>
                            <div class="si-preview">{{ $mediaLabels[$mediaType] }} from every support chat</div>
                        </div>
                        <div class="si-meta"><span class="si-badge">{{ number_format($mediaConversationCounts->sum()) }}</span></div>
                    </div>
                    @foreach($conversations->filter(fn ($c) => ($mediaConversationCounts[$c['id']] ?? 0) > 0) as $c)
                        <div class="si-item {{ $mediaConversationId == $c['id'] ? 'active' : '' }}"
                             wire:click="setMediaConversation({{ $c['id'] }})" data-si-open
                             wire:key="si-media-conv-{{ $c['id'] }}">
                            <div class="si-avatar" style="--si-av: {{ user_avatar_color($c['customer_id'] ?? $c['customer_name']) }}">
                                {{ strtoupper(substr($c['customer_name'] ?? '?', 0, 1)) }}
                                @if(!empty($c['customer_avatar']))
                                    <img src="{{ $c['customer_avatar'] }}" alt="" wire:key="si-av-{{ md5($c['customer_avatar']) }}" wire:ignore onerror="this.style.display='none'">
                                @endif
                            </div>
                            <div class="si-info">
                                <div class="si-name">
                                    {{ $c['customer_name'] }}
                                    @if(!empty($c['customer_country']))<span class="si-country-prefix">{{ $c['customer_country'] }}</span>@endif
                                </div>
                                <div class="si-preview">{{ $c['customer_email'] }}</div>
                            </div>
                            <div class="si-meta"><span class="si-badge">{{ $mediaConversationCounts[$c['id']] }}</span></div>
                        </div>
                    @endforeach
                @else
                    @php $me = auth()->user(); @endphp
                    <button type="button" class="si-set-profile is-link" wire:click="setSettingsSection('account')" data-si-open title="Edit your profile">
                        <span class="si-avatar">
                            @if($meAvatarUrl)
                                <img src="{{ $meAvatarUrl }}" alt="">
                            @else
                                {{ strtoupper(substr($me->name ?: $me->email, 0, 1)) }}
                            @endif
                        </span>
                        <span style="min-width:0;">
                            <span class="si-set-profile-name" style="display:block;">{{ $me->name ?: $me->email }}</span>
                            <span class="si-set-profile-sub" style="display:block;">Support agent · {{ $me->email }}</span>
                        </span>
                    </button>
                    @php
                        $settingsItems = [
                            ['id' => 'account', 'icon' => 'fa-user', 'title' => 'Account', 'sub' => 'Profile photo, name, phone number', 'danger' => false],
                            ['id' => 'tools', 'icon' => 'fa-briefcase', 'title' => 'Business tools', 'sub' => 'Broadcast, quick & auto replies, AI bots', 'danger' => false],
                            ['id' => 'calls', 'icon' => $supportCallsBlocked ? 'fa-phone-slash' : 'fa-phone', 'title' => 'Calls',
                             'sub' => $supportCallsBlocked ? 'All support calls are paused' : 'Support calls are available', 'danger' => $supportCallsBlocked],
                            ['id' => 'blocked', 'icon' => 'fa-ban', 'title' => 'Blocked',
                             'sub' => $blockedCustomers->count() . ' ' . \Illuminate\Support\Str::plural('customer', $blockedCustomers->count()) . " can't place calls", 'danger' => false],
                        ];
                    @endphp
                    @foreach($settingsItems as $item)
                        {{-- Quick / auto replies live under Business tools, so keep that row lit. --}}
                        <button type="button" class="si-set-row {{ ($settingsSection === $item['id'] || ($item['id'] === 'tools' && in_array($settingsSection, ['quick-replies', 'auto-reply', 'topup-message'], true))) && !$starredViewOpen ? 'active' : '' }}"
                                wire:click="setSettingsSection('{{ $item['id'] }}')" data-si-open wire:key="si-set-{{ $item['id'] }}">
                            <span class="si-set-row-icon"><i class="fa {{ $item['icon'] }}"></i></span>
                            <span class="si-set-row-body">
                                <span class="si-set-row-title">{{ $item['title'] }}</span>
                                <span class="si-set-row-sub {{ $item['danger'] ? 'danger' : '' }}">{{ $item['sub'] }}</span>
                            </span>
                            <i class="fa fa-angle-right si-set-row-arrow"></i>
                        </button>
                    @endforeach
                    <button type="button" class="si-set-row {{ $starredViewOpen ? 'active' : '' }}" wire:click="openStarredView" data-si-open wire:key="si-set-starred">
                        <span class="si-set-row-icon"><i class="fa fa-star"></i></span>
                        <span class="si-set-row-body">
                            <span class="si-set-row-title">Starred messages</span>
                            <span class="si-set-row-sub">Messages you starred in support chats</span>
                        </span>
                        <i class="fa fa-angle-right si-set-row-arrow"></i>
                    </button>
                @endif
            </div>

            {{-- Icon strip — same four destinations and icons as my-chat's .chat-sidebar-tabs. --}}
            <div class="si-bottom-tabs" role="tablist" aria-label="Support inbox views">
                <button type="button" class="si-bottom-tab {{ $inboxTab === 'chat' ? 'active' : '' }}" wire:click="setInboxTab('chat')"
                        data-si-tab role="tab" aria-selected="{{ $inboxTab === 'chat' ? 'true' : 'false' }}" title="Chats" aria-label="Chats">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M1.53846 16.8005L4.26683 14.6154H16.9231C17.3311 14.6154 17.7224 14.4533 18.0109 14.1648C18.2995 13.8763 18.4615 13.485 18.4615 13.077V3.07695C18.4615 2.66893 18.2995 2.27761 18.0109 1.9891C17.7224 1.70058 17.3311 1.53849 16.9231 1.53849H3.07692C2.6689 1.53849 2.27758 1.70058 1.98907 1.9891C1.70055 2.27761 1.53846 2.66893 1.53846 3.07695V16.8005ZM4.80769 16.1539L1.25 19C1.13684 19.0906 1.00038 19.1474 0.856346 19.1638C0.712315 19.1803 0.566576 19.1556 0.435927 19.0928C0.305279 19.03 0.195037 18.9316 0.11791 18.8088C0.0407831 18.6861 -9.05108e-05 18.544 1.50494e-07 18.3991V3.07695C1.50494e-07 2.2609 0.324175 1.47828 0.90121 0.90124C1.47825 0.324205 2.26087 3.05176e-05 3.07692 3.05176e-05H16.9231C17.7391 3.05176e-05 18.5218 0.324205 19.0988 0.90124C19.6758 1.47828 20 2.2609 20 3.07695V13.077C20 13.893 19.6758 14.6756 19.0988 15.2527C18.5218 15.8297 17.7391 16.1539 16.9231 16.1539H4.80769Z" fill="currentColor"/>
                        <path d="M6.154 9.2308H13.8463C14.3591 9.2308 14.6155 9.48721 14.6155 10C14.6155 10.5129 14.3591 10.7693 13.8463 10.7693H6.154C5.64118 10.7693 5.38477 10.5129 5.38477 10C5.38477 9.48721 5.64118 9.2308 6.154 9.2308ZM6.154 4.61542H13.8463C14.3591 4.61542 14.6155 4.87183 14.6155 5.38465C14.6155 5.89747 14.3591 6.15388 13.8463 6.15388H6.154C5.64118 6.15388 5.38477 5.89747 5.38477 5.38465C5.38477 4.87183 5.64118 4.61542 6.154 4.61542Z" fill="currentColor"/>
                    </svg>
                    <span class="si-bottom-tab-label">Chats</span>
                    @if($unreadChatCount > 0)<span class="si-tab-badge">{{ $unreadChatCount }}</span>@endif
                </button>
                <button type="button" class="si-bottom-tab {{ $inboxTab === 'calls' ? 'active' : '' }}" wire:click="setInboxTab('calls')"
                        data-si-tab role="tab" aria-selected="{{ $inboxTab === 'calls' ? 'true' : 'false' }}" title="Calls" aria-label="Calls">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M18.1891 14.8547C17.502 14.1624 15.8378 13.1521 15.0304 12.745C13.979 12.2154 13.8924 12.1721 13.0659 12.7861C12.5147 13.1958 12.1482 13.5619 11.503 13.4243C10.8579 13.2867 9.45591 12.5109 8.22833 11.2873C7.00075 10.0637 6.17991 8.62116 6.04188 7.9782C5.90385 7.33525 6.27597 6.9731 6.68185 6.42058C7.25388 5.64177 7.21061 5.51196 6.72166 4.46057C6.34045 3.64281 5.30066 1.99433 4.60574 1.3107C3.86236 0.576455 3.86236 0.706257 3.38336 0.905287C2.99338 1.06932 2.61925 1.26875 2.26568 1.50108C1.57336 1.96101 1.18912 2.34306 0.920411 2.91722C0.651703 3.49138 0.530979 4.83743 1.91866 7.35818C3.30633 9.87894 4.27992 11.1679 6.29501 13.1772C8.31011 15.1865 9.85962 16.2669 12.1248 17.5373C14.927 19.1066 16.0018 18.8007 16.5778 18.5324C17.1537 18.2642 17.5375 17.8834 17.9983 17.1911C18.2313 16.8382 18.4312 16.4645 18.5955 16.0748C18.7949 15.5976 18.9247 15.5976 18.1891 14.8547Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10"/>
                    </svg>
                    <span class="si-bottom-tab-label">Calls</span>
                    @if($unansweredCallsCount > 0)<span class="si-tab-badge">{{ $unansweredCallsCount }}</span>@endif
                </button>
                <button type="button" class="si-bottom-tab {{ $inboxTab === 'status' ? 'active' : '' }}" wire:click="setInboxTab('status')"
                        data-si-tab role="tab" aria-selected="{{ $inboxTab === 'status' ? 'true' : 'false' }}" title="Status" aria-label="Status">
                    <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M18.5694 6.81786L18.5877 6.87039L18.6553 7.09736L18.7119 7.30107L18.7361 7.3941L18.7756 7.56252C19.2889 9.90945 18.9143 12.3661 17.7257 14.4465C16.5398 16.5334 14.6229 18.0922 12.3556 18.8135L12.1957 18.8616L11.9994 18.9129L11.7156 18.9811C11.6154 19.0042 11.5116 19.0062 11.4106 18.9868C11.3096 18.9674 11.2136 18.9272 11.1286 18.8686C11.0437 18.8099 10.9715 18.7342 10.9167 18.6461C10.862 18.5579 10.8257 18.4593 10.8102 18.3563C10.7784 18.145 10.826 17.9292 10.9437 17.7518C11.0614 17.5744 11.2406 17.4482 11.4457 17.3983L11.6741 17.3402C11.8166 17.3022 11.9391 17.2657 12.0417 17.2307C13.8626 16.6077 15.3964 15.3325 16.3549 13.6445C17.319 11.9578 17.6416 9.97224 17.2619 8.06137L17.2346 7.93586L17.1982 7.78949L17.1527 7.62187L17.0981 7.4334C17.0369 7.22781 17.0542 7.00643 17.1466 6.81316C17.239 6.6199 17.3996 6.46886 17.5967 6.38999C17.6883 6.35332 17.7861 6.33531 17.8846 6.33697C17.9831 6.33863 18.0802 6.35994 18.1706 6.39967C18.2609 6.43941 18.3426 6.49679 18.4111 6.56855C18.4795 6.6403 18.5333 6.72502 18.5694 6.81786ZM1.33976 6.38398C1.35691 6.38932 1.37379 6.39521 1.39041 6.40162C1.58708 6.47929 1.74777 6.62899 1.84064 6.82106C1.93351 7.01313 1.95177 7.23354 1.89181 7.43861C1.82058 7.68162 1.76913 7.88386 1.73748 8.04533C1.35944 9.95468 1.6824 11.9382 2.6457 13.6233C3.60507 15.3128 5.14069 16.5887 6.96361 17.2111L7.0847 17.25L7.15356 17.27L7.3079 17.3125L7.58254 17.3811C7.78887 17.4302 7.96943 17.5562 8.0884 17.7339C8.20737 17.9117 8.25607 18.1283 8.22483 18.3407C8.19551 18.5399 8.08933 18.7191 7.92962 18.839C7.76992 18.9588 7.56977 19.0095 7.3732 18.9799L7.31858 18.9699L7.09064 18.9157L6.88881 18.8636L6.71231 18.8135L6.63316 18.7894C4.37043 18.0662 2.45806 16.5086 1.27446 14.4249C0.0837235 12.3408 -0.290189 9.87928 0.226939 7.52884L0.257411 7.40052L0.296985 7.25014L0.373758 6.98348L0.403835 6.88323C0.432902 6.78814 0.48017 6.69979 0.542939 6.62322C0.605707 6.54665 0.682747 6.48335 0.769658 6.43696C0.856569 6.39056 0.951649 6.36196 1.04947 6.3528C1.14729 6.34364 1.24593 6.3545 1.33976 6.38398ZM9.5181 3.20803C13.0152 3.20803 15.8499 6.08042 15.8499 9.62408C15.8499 13.1677 13.0152 16.0401 9.5181 16.0401C6.02096 16.0401 3.18628 13.1677 3.18628 9.62408C3.18628 6.08042 6.02135 3.20803 9.5181 3.20803ZM9.5181 4.81204C6.89554 4.81204 4.76923 6.96663 4.76923 9.62408C4.76923 12.2815 6.89554 14.4361 9.5181 14.4361C12.1411 14.4361 14.267 12.2815 14.267 9.62408C14.267 6.96663 12.1411 4.81204 9.5181 4.81204ZM9.5181 7.22374e-06C11.8947 -0.00294597 14.1855 0.899685 15.9358 2.52873L16.0304 2.61976L16.1392 2.72964L16.2619 2.85876L16.3988 3.00673C16.4687 3.08311 16.5222 3.17326 16.5562 3.27155C16.5901 3.36984 16.6037 3.47417 16.5961 3.57801C16.5885 3.68186 16.5598 3.78301 16.5119 3.87514C16.464 3.96727 16.3979 4.04841 16.3177 4.1135C16.153 4.24723 15.9447 4.31337 15.7341 4.29881C15.5235 4.28426 15.326 4.19006 15.1807 4.0349C15.0947 3.94267 15.0149 3.8602 14.9413 3.78748L14.8028 3.65355L14.7395 3.5962C13.2967 2.30971 11.44 1.60129 9.5181 1.60402C7.59446 1.60119 5.73612 2.31084 4.29276 3.59941L4.24765 3.64031L4.14753 3.73655L4.03395 3.85084L3.83806 4.05736C3.5373 4.38177 3.04104 4.41826 2.69714 4.14157C2.62002 4.07953 2.55572 4.0027 2.50791 3.91546C2.4601 3.82823 2.42972 3.73231 2.41851 3.63317C2.4073 3.53404 2.41548 3.43364 2.44258 3.33771C2.46968 3.24178 2.51517 3.15221 2.57644 3.0741L2.61206 3.03159L2.77233 2.85876L2.91797 2.70718C2.98709 2.63687 3.05093 2.57471 3.1095 2.52071C4.8586 0.896831 7.14546 -0.00272003 9.51771 7.22374e-06" fill="currentColor"/>
                    </svg>
                    <span class="si-bottom-tab-label">Status</span>
                </button>
                <button type="button" class="si-bottom-tab {{ $inboxTab === 'media' ? 'active' : '' }}" wire:click="setInboxTab('media')"
                        data-si-tab role="tab" aria-selected="{{ $inboxTab === 'media' ? 'true' : 'false' }}" title="Media" aria-label="Media">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12.3006 12.4248C12.9348 12.4248 13.5247 12.3081 14.0701 12.0748C14.6156 11.8415 15.1058 11.5188 15.5408 11.1068C15.905 10.7606 16.2053 10.368 16.4418 9.92906C16.6784 9.49011 16.8422 9.01713 16.9332 8.51014C16.9705 8.35759 16.9354 8.22411 16.828 8.1097C16.7213 7.99529 16.5892 7.94967 16.4318 7.97286C15.9177 8.03268 15.4341 8.17476 14.9812 8.39909C14.5283 8.62343 14.1197 8.90908 13.7556 9.25605C13.3266 9.66733 12.9863 10.144 12.7348 10.6862C12.4834 11.2283 12.3386 11.8079 12.3006 12.4248ZM12.3006 12.4248C12.2632 11.8079 12.1189 11.2283 11.8674 10.6862C11.6159 10.144 11.2753 9.66733 10.8455 9.25605C10.4814 8.90983 10.0728 8.62455 9.61991 8.40021C9.16698 8.17588 8.68383 8.03343 8.17046 7.97286C8.01227 7.94967 7.87982 7.99529 7.77312 8.1097C7.66642 8.22411 7.63172 8.35759 7.66903 8.51014C7.7623 9.01713 7.93019 9.49011 8.1727 9.92906C8.41521 10.368 8.71853 10.7606 9.08266 11.1068C9.51172 11.5181 9.99673 11.8408 10.5377 12.0748C11.0787 12.3089 11.6663 12.4255 12.3006 12.4248ZM12.3006 9.05976C12.6177 9.05976 12.8837 8.95245 13.0986 8.73784C13.3135 8.52322 13.4206 8.25664 13.4198 7.93808V7.65767L13.6996 7.76983C13.9795 7.882 14.2641 7.91004 14.5536 7.85396C14.8432 7.79787 15.0622 7.63897 15.2107 7.37725C15.3785 7.09683 15.4345 6.79772 15.3785 6.47991C15.3226 6.1621 15.136 5.93851 14.8189 5.80915L14.5391 5.69922L14.8189 5.58818C15.136 5.45881 15.3215 5.23298 15.3752 4.91069C15.4297 4.58764 15.3748 4.28816 15.2107 4.01223C15.0577 3.74602 14.8447 3.58263 14.5716 3.52205C14.2984 3.46148 14.0078 3.49401 13.6996 3.61964L13.4198 3.73181V3.45139C13.4198 3.13358 13.3127 2.86737 13.0986 2.65276C12.8844 2.43814 12.6184 2.33046 12.3006 2.32972C11.9827 2.32897 11.717 2.43665 11.5036 2.65276C11.2902 2.86887 11.1828 3.13508 11.1813 3.45139V3.73181L10.9015 3.61964C10.5933 3.49252 10.3027 3.45962 10.0296 3.52093C9.75721 3.58225 9.54455 3.74602 9.39158 4.01223C9.22742 4.28891 9.1722 4.58802 9.22593 4.90956C9.27965 5.23261 9.46508 5.45881 9.78221 5.58818L10.062 5.69922L9.78221 5.80915C9.46508 5.93851 9.27853 6.1621 9.22257 6.47991C9.16661 6.79772 9.22257 7.09683 9.39046 7.37725C9.5397 7.63897 9.75907 7.79787 10.0486 7.85396C10.3381 7.91004 10.6224 7.882 10.9015 7.76983L11.1813 7.65767V7.93808C11.1813 8.25589 11.2887 8.52248 11.5036 8.73784C11.7185 8.9532 11.9842 9.0605 12.3006 9.05976ZM12.3006 6.81641C11.9834 6.81641 11.7178 6.7091 11.5036 6.49449C11.2895 6.27988 11.182 6.01329 11.1813 5.69474C11.1805 5.37618 11.288 5.10997 11.5036 4.8961C11.7193 4.68224 11.9849 4.57456 12.3006 4.57306C12.6162 4.57157 12.8822 4.67925 13.0986 4.8961C13.315 5.11296 13.4221 5.37917 13.4198 5.69474C13.4176 6.0103 13.3105 6.27689 13.0986 6.49449C12.8867 6.71209 12.6207 6.8194 12.3006 6.81641ZM6.74115 15.1C6.22629 15.1 5.79612 14.9269 5.45064 14.5806C5.10516 14.2344 4.93279 13.8033 4.93354 13.2873V1.81262C4.93354 1.29591 5.1059 0.86481 5.45064 0.519335C5.79537 0.173859 6.22554 0.000747782 6.74115 0H18.1913C18.7069 0 19.137 0.173112 19.4818 0.519335C19.8265 0.865558 19.9993 1.29665 20 1.81262V13.2873C20 13.8033 19.8273 14.2344 19.4818 14.5806C19.1363 14.9269 18.7061 15.1 18.1913 15.1H6.74115ZM6.74115 13.9783H18.1913C18.3927 13.9783 18.558 13.9136 18.6871 13.7842C18.8162 13.6549 18.8807 13.4892 18.8807 13.2873V1.81262C18.8807 1.61072 18.8162 1.44509 18.6871 1.31572C18.558 1.18636 18.3931 1.12167 18.1924 1.12167H6.74115C6.53969 1.12167 6.37478 1.18636 6.24644 1.31572C6.11735 1.44509 6.0528 1.61072 6.0528 1.81262V13.2873C6.0528 13.4892 6.11735 13.6549 6.24644 13.7842C6.37553 13.9136 6.54043 13.9783 6.74115 13.9783ZM3.41245 19.9849C2.91102 20.0454 2.46331 19.9243 2.06933 19.6214C1.67535 19.3186 1.44851 18.917 1.38882 18.4168L0.0143553 7.52194C-0.0453389 7.02018 0.0811383 6.5659 0.393787 6.15911C0.706436 5.75157 1.1131 5.52275 1.61379 5.47265L2.10067 5.45021C2.25961 5.4375 2.40026 5.48237 2.52263 5.58481C2.64575 5.68726 2.70731 5.8241 2.70731 5.99535C2.70731 6.13892 2.65956 6.26679 2.56405 6.37896C2.46854 6.49113 2.35288 6.55357 2.21707 6.56628L1.78056 6.58759C1.57909 6.60254 1.4209 6.68517 1.30599 6.83548C1.19108 6.98653 1.14817 7.16263 1.17727 7.36379L2.51592 18.2665C2.54427 18.4676 2.63008 18.6258 2.77335 18.7409C2.91736 18.8561 3.08973 18.8991 3.29045 18.8699L16.4676 17.2211C16.6273 17.1986 16.7683 17.2341 16.8907 17.3276C17.013 17.4211 17.0839 17.5478 17.1033 17.7079C17.1257 17.8671 17.0903 18.0036 16.997 18.1173C16.9037 18.2309 16.7776 18.2971 16.6187 18.3158L3.41245 19.9849Z" fill="currentColor"/>
                    </svg>
                    <span class="si-bottom-tab-label">Media</span>
                </button>
                <button type="button" class="si-bottom-tab {{ $inboxTab === 'settings' ? 'active' : '' }}" wire:click="setInboxTab('settings')"
                        data-si-tab role="tab" aria-selected="{{ $inboxTab === 'settings' ? 'true' : 'false' }}" title="Settings" aria-label="Settings">
                    <svg viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M17.7623 11.9151L16.3723 10.7275C16.4381 10.3245 16.4721 9.91313 16.4721 9.50172C16.4721 9.09031 16.4381 8.67889 16.3723 8.27597L17.7623 7.08838C17.8672 6.99869 17.9422 6.87923 17.9775 6.74589C18.0127 6.61255 18.0065 6.47164 17.9597 6.3419L17.9406 6.28676C17.5579 5.21799 16.9849 4.22722 16.2492 3.36234L16.211 3.31781C16.1218 3.21294 16.0028 3.13756 15.8699 3.1016C15.7369 3.06564 15.5961 3.07078 15.4662 3.11634L13.7409 3.72922C13.1042 3.20753 12.3933 2.79612 11.6251 2.50771L11.2919 0.705127C11.2668 0.569493 11.2009 0.444712 11.1031 0.347363C11.0053 0.250014 10.8802 0.184705 10.7444 0.160112L10.6871 0.149508C9.58147 -0.049836 8.41853 -0.049836 7.3129 0.149508L7.2556 0.160112C7.11978 0.184705 6.99466 0.250014 6.89686 0.347363C6.79907 0.444712 6.73323 0.569493 6.70809 0.705127L6.37279 2.51619C5.61071 2.80468 4.90102 3.21587 4.27187 3.73346L2.53384 3.11634C2.40389 3.07041 2.26304 3.06509 2.12999 3.10107C1.99694 3.13706 1.878 3.21265 1.78897 3.31781L1.75077 3.36234C1.01596 4.22784 0.443048 5.21844 0.0594242 6.28676L0.0403249 6.3419C-0.0551714 6.60699 0.0233479 6.90388 0.237684 7.08838L1.64466 8.28869C1.57888 8.68738 1.54704 9.09455 1.54704 9.4996C1.54704 9.90677 1.57888 10.3139 1.64466 10.7105L0.237684 11.9108C0.132841 12.0005 0.0578039 12.12 0.0225499 12.2533C-0.0127041 12.3867 -0.00650436 12.5276 0.0403249 12.6573L0.0594242 12.7124C0.443532 13.7813 1.01227 14.7674 1.75077 15.6369L1.78897 15.6814C1.87821 15.7863 1.99716 15.8616 2.13013 15.8976C2.2631 15.9336 2.40385 15.9284 2.53384 15.8829L4.27187 15.2657C4.90427 15.7853 5.61094 16.1967 6.37279 16.483L6.70809 18.2941C6.73323 18.4297 6.79907 18.5545 6.89686 18.6518C6.99466 18.7492 7.11978 18.8145 7.2556 18.8391L7.3129 18.8497C8.42869 19.0501 9.57131 19.0501 10.6871 18.8497L10.7444 18.8391C10.8802 18.8145 11.0053 18.7492 11.1031 18.6518C11.2009 18.5545 11.2668 18.4297 11.2919 18.2941L11.6251 16.4915C12.393 16.2038 13.1079 15.7911 13.7409 15.27L15.4662 15.8829C15.5961 15.9288 15.737 15.9341 15.87 15.8981C16.0031 15.8621 16.122 15.7865 16.211 15.6814L16.2492 15.6369C16.9877 14.7653 17.5565 13.7813 17.9406 12.7124L17.9597 12.6573C18.0552 12.3965 17.9767 12.0996 17.7623 11.9151ZM14.8656 8.52621C14.9186 8.84643 14.9462 9.17513 14.9462 9.50384C14.9462 9.83255 14.9186 10.1613 14.8656 10.4815L14.7255 11.3319L16.3108 12.687C16.0705 13.2403 15.7671 13.764 15.4067 14.2478L13.4374 13.5501L12.771 14.0972C12.2639 14.5129 11.6994 14.8395 11.0882 15.0685L10.2797 15.3718L9.89979 17.4288C9.30043 17.4967 8.69532 17.4967 8.09597 17.4288L7.71611 15.3675L6.91394 15.06C6.30913 14.831 5.74676 14.5044 5.24381 14.0909L4.57746 13.5416L2.59538 14.2457C2.23462 13.7601 1.93327 13.2362 1.69135 12.6849L3.29357 11.317L3.15563 10.4688C3.1047 10.1528 3.07711 9.82618 3.07711 9.50384C3.07711 9.17938 3.10257 8.85491 3.15563 8.53893L3.29357 7.69066L1.69135 6.32282C1.93115 5.76932 2.23462 5.24763 2.59538 4.76199L4.57746 5.46606L5.24381 4.9168C5.74676 4.50327 6.30913 4.17668 6.91394 3.94765L7.71823 3.64439L8.09809 1.58309C8.69441 1.51523 9.30347 1.51523 9.90191 1.58309L10.2818 3.64015L11.0903 3.94341C11.6994 4.17244 12.266 4.49903 12.7732 4.91468L13.4395 5.46182L15.4089 4.76411C15.7696 5.24975 16.071 5.77356 16.3129 6.32494L14.7277 7.68005L14.8656 8.52621ZM9.00212 5.55937C6.9394 5.55937 5.26716 7.23047 5.26716 9.29177C5.26716 11.3531 6.9394 13.0242 9.00212 13.0242C11.0648 13.0242 12.7371 11.3531 12.7371 9.29177C12.7371 7.23047 11.0648 5.55937 9.00212 5.55937ZM10.6829 10.9714C10.4624 11.1923 10.2004 11.3675 9.91199 11.4868C9.62354 11.6062 9.31433 11.6674 9.00212 11.6669C8.3676 11.6669 7.77128 11.4188 7.32139 10.9714C7.10031 10.7511 6.925 10.4893 6.80554 10.201C6.68609 9.91277 6.62484 9.60376 6.62533 9.29177C6.62533 8.65769 6.87362 8.06178 7.32139 7.61219C7.77128 7.16261 8.3676 6.91661 9.00212 6.91661C9.63664 6.91661 10.233 7.16261 10.6829 7.61219C10.9039 7.83248 11.0792 8.09428 11.1987 8.38253C11.3182 8.67078 11.3794 8.97978 11.3789 9.29177C11.3789 9.92586 11.1306 10.5218 10.6829 10.9714Z" fill="currentColor"/>
                    </svg>
                    <span class="si-bottom-tab-label">Settings</span>
                    @if($supportCallsBlocked)<span class="si-tab-dot" title="Support calls are paused"></span>@endif
                </button>
            </div>
        </div>

        {{-- Right: message area --}}
        <div class="si-main">
            {{-- Starred-messages overlay — covers the message area while open.
                 Lists every message this admin has starred across support
                 conversations; click a row to jump to that conversation. --}}
            @if($starredViewOpen)
                @php $starred = $this->getStarredMessages(); @endphp
                <div class="si-starred-panel">
                    <div class="si-starred-head">
                        <button type="button" class="si-starred-back" wire:click="closeStarredView" @if($settingsSection === '') data-si-back @endif aria-label="Back">
                            <i class="fa fa-angle-left"></i>
                        </button>
                        <h3>Starred messages</h3>
                    </div>
                    <div class="si-starred-body">
                        @if($starred->isEmpty())
                            <div class="si-empty" style="padding:40px 20px;">
                                <i class="fa fa-star"></i>
                                <h4>No starred messages yet</h4>
                                <p>Open a chat, tap a message's menu and choose Star to save it here.</p>
                            </div>
                        @else
                            <div class="si-starred-list">
                                @foreach($starred as $s)
                                    <button type="button" class="si-starred-item" wire:click="selectConversation({{ $s['conversation_id'] }})">
                                        <div class="si-starred-meta">
                                            <span class="si-starred-sender">{{ $s['is_mine'] ? 'You' : $s['sender_name'] }}</span>
                                            <span class="si-starred-arrow">&rsaquo;</span>
                                            <span class="si-starred-peer">{{ $s['peer_name'] }}</span>
                                            <span class="si-starred-date">{{ \Carbon\Carbon::parse($s['created_at'])->diffForHumans() }}</span>
                                        </div>
                                        <div class="si-starred-body-line">
                                            @if($s['attachment_type'] === 'image')
                                                <i class="fa fa-image"></i> Photo
                                            @elseif($s['attachment_type'] === 'audio')
                                                <i class="fa fa-microphone"></i> Voice message
                                            @elseif($s['attachment_type'] === 'video')
                                                <i class="fa fa-video"></i> Video
                                            @elseif(!empty($s['message']))
                                                {{ \Illuminate\Support\Str::limit($s['message'], 120) }}
                                            @else
                                                <i class="fa fa-paperclip"></i> Attachment
                                            @endif
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @if($inboxTab === 'media')
                {{-- Media, links & docs across all support chats, grouped by day
                     like WhatsApp. Clicking an item opens its chat on that message. --}}
                <div class="si-pane">
                    <div class="si-pane-head">
                        <button type="button" class="si-pane-back si-mobile-only" wire:click="closeMediaPane" data-si-back title="Back" aria-label="Back">
                            <i class="fa fa-arrow-left"></i>
                        </button>
                        <div style="flex:1; min-width:0;">
                            <div class="si-pane-title">Media, links and docs</div>
                            <div class="si-pane-sub">{{ $mediaCustomerName ?? 'All support chats' }}</div>
                        </div>
                        @if($mediaConversationId)
                            <button type="button" class="si-set-btn" wire:click="setMediaConversation">
                                <i class="fa fa-times"></i> All customers
                            </button>
                        @endif
                    </div>
                    <div class="si-media-tabs" role="tablist" aria-label="Media types">
                        @foreach($mediaLabels as $type => $label)
                            <button type="button" class="si-media-tab {{ $mediaType === $type ? 'active' : '' }}"
                                    wire:click="setMediaType('{{ $type }}')" role="tab" aria-selected="{{ $mediaType === $type ? 'true' : 'false' }}">
                                {{ $label }} <small>{{ number_format($mediaTypeCounts[$type] ?? 0) }}</small>
                            </button>
                        @endforeach
                    </div>
                    <div class="si-media-body">
                        @forelse($mediaItems->groupBy('date_key') as $dateKey => $items)
                            <section wire:key="si-media-group-{{ $mediaType }}-{{ $dateKey }}">
                                <div class="si-media-date">{{ $items->first()['date_label'] }}</div>
                                @if($mediaType === 'media')
                                    <div class="si-media-grid">
                                        @foreach($items as $item)
                                            <button type="button" class="si-media-tile" wire:key="si-media-{{ $item['id'] }}"
                                                    wire:click="openMediaMessage({{ $item['id'] }})"
                                                    title="{{ $item['customer_name'] }} · {{ $item['created_at']->format('h:i A') }}">
                                                {{-- wire:ignore keeps the loaded <img>/<video> out of later morphs so
                                                     thumbnails don't blank and re-fetch on every inbox refresh. --}}
                                                <span class="si-media-tile-inner" wire:ignore>
                                                    @if($item['type'] === 'video')
                                                        @if($item['thumbnail_url'])
                                                            <img src="{{ $item['thumbnail_url'] }}" alt="" decoding="async">
                                                        @else
                                                            <video src="{{ $item['url'] }}" preload="metadata" muted playsinline></video>
                                                        @endif
                                                    @else
                                                        <img src="{{ $item['url'] }}" alt="" decoding="async">
                                                    @endif
                                                </span>
                                                @if($item['type'] === 'video')
                                                    <span class="si-media-tile-play"><i class="fa fa-play"></i></span>
                                                    @if($item['duration'])<span class="si-media-tile-dur">{{ gmdate('i:s', (int) $item['duration']) }}</span>@endif
                                                @endif
                                                <span class="si-media-tile-cap">
                                                    <span>{{ $item['from_support'] ? 'Support' : $item['customer_name'] }}</span>
                                                    <span>{{ $item['created_at']->format('h:i A') }}</span>
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="si-media-rows">
                                        @foreach($items as $item)
                                            @php
                                                $direction = $item['from_support'] ? 'Support → ' . $item['customer_name'] : $item['customer_name'];
                                                $ext = strtolower(pathinfo((string) $item['name'], PATHINFO_EXTENSION));
                                                $docIcon = match (true) {
                                                    $ext === 'pdf' => 'fa-file-pdf',
                                                    in_array($ext, ['doc', 'docx'], true) => 'fa-file-word',
                                                    in_array($ext, ['xls', 'xlsx', 'csv'], true) => 'fa-file-excel',
                                                    in_array($ext, ['ppt', 'pptx'], true) => 'fa-file-powerpoint',
                                                    in_array($ext, ['zip', 'rar'], true) => 'fa-file-archive',
                                                    default => 'fa-file-alt',
                                                };
                                            @endphp
                                            <div class="si-media-row" role="button" tabindex="0" wire:key="si-media-{{ $item['id'] }}"
                                                 wire:click="openMediaMessage({{ $item['id'] }})" wire:keydown.enter="openMediaMessage({{ $item['id'] }})"
                                                 title="Open in chat">
                                                @if($mediaType === 'audio')
                                                    <span class="si-media-row-icon audio"><i class="fa fa-microphone"></i></span>
                                                    <div class="si-media-row-body">
                                                        <div class="si-media-row-title">
                                                            {{ $item['name'] && !str_starts_with($item['name'], 'voice-') ? $item['name'] : 'Voice message' }}
                                                        </div>
                                                        <div class="si-media-row-sub">
                                                            {{ $direction }}@if($item['duration']) · {{ gmdate('i:s', (int) $item['duration']) }}@endif
                                                        </div>
                                                    </div>
                                                @elseif($mediaType === 'docs')
                                                    <span class="si-media-row-icon doc"><i class="fa {{ $docIcon }}"></i></span>
                                                    <div class="si-media-row-body">
                                                        <div class="si-media-row-title">{{ $item['name'] ?: 'Document' }}</div>
                                                        <div class="si-media-row-sub">
                                                            {{ $direction }}@if($item['size']) · {{ number_format($item['size'] / 1024, 1) }} KB @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="si-media-row-icon link"><i class="fa fa-link"></i></span>
                                                    <div class="si-media-row-body">
                                                        <div class="si-media-row-title">
                                                            @if($item['link'])
                                                                <a href="{{ $item['link'] }}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation()">{{ $item['link'] }}</a>
                                                            @else
                                                                {{ \Illuminate\Support\Str::limit($item['message'], 60) }}
                                                            @endif
                                                        </div>
                                                        <div class="si-media-row-sub">{{ $direction }} · {{ \Illuminate\Support\Str::limit($item['message'], 90) }}</div>
                                                    </div>
                                                @endif
                                                <span class="si-media-row-time">{{ $item['created_at']->format('h:i A') }}</span>
                                                @if($mediaType !== 'links' && $item['url'])
                                                    <a href="{{ $item['url'] }}" class="si-media-row-dl" target="_blank" rel="noopener" download="{{ $item['name'] }}"
                                                       onclick="event.stopPropagation()" title="Download" aria-label="Download"><i class="fa fa-download"></i></a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </section>
                        @empty
                            @php
                                [$emptyIcon, $emptyText] = match ($mediaType) {
                                    'audio' => ['fa-microphone', 'Voice notes and audio files sent in support chats will appear here.'],
                                    'docs' => ['fa-file-alt', 'Documents sent in support chats will appear here.'],
                                    'links' => ['fa-link', 'Messages containing links will appear here.'],
                                    default => ['fa-images', 'Photos and videos sent in support chats will appear here.'],
                                };
                            @endphp
                            <div class="si-empty" style="padding:60px 20px;">
                                <i class="fa {{ $emptyIcon }}"></i>
                                <h4>No {{ strtolower($mediaLabels[$mediaType]) }} yet</h4>
                                <p>{{ $emptyText }}</p>
                            </div>
                        @endforelse
                        @if($mediaHasMore)
                            <div class="si-media-more">
                                <button type="button" class="si-set-btn" wire:click="loadMoreMedia" wire:loading.attr="disabled" wire:target="loadMoreMedia">
                                    <span wire:loading.remove wire:target="loadMoreMedia"><i class="fa fa-history"></i> Load older</span>
                                    <span wire:loading wire:target="loadMoreMedia"><i class="fa fa-spinner fa-spin"></i> Loading…</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif($inboxTab === 'status')
                <div class="si-pane">
                    @if($statusComposerOpen)
                        <div class="si-pane-head">
                            <button type="button" class="si-pane-back" wire:click="closeStatusComposer" data-si-back title="Back" aria-label="Back">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            <div>
                                <div class="si-pane-title">New text status</div>
                                <div class="si-pane-sub">Customers can see it for 24 hours</div>
                            </div>
                        </div>
                        <form class="si-status-stage" wire:submit.prevent="postTextStatus">
                            <div class="si-status-card" style="background: {{ $statusBg }};">
                                <textarea id="siStatusText" wire:model="statusText" maxlength="500" placeholder="Type a status" aria-label="Status text" autofocus></textarea>
                            </div>
                            <div class="si-status-swatches" role="radiogroup" aria-label="Background colour">
                                @foreach(\App\Livewire\Admin\SupportInbox::STATUS_COLORS as $color)
                                    <button type="button" class="si-status-swatch {{ $statusBg === $color ? 'active' : '' }}" style="background: {{ $color }};"
                                            wire:click="setStatusBg('{{ $color }}')" role="radio" aria-checked="{{ $statusBg === $color ? 'true' : 'false' }}" aria-label="Background {{ $color }}"></button>
                                @endforeach
                            </div>
                            @error('statusText') <div class="si-status-error">{{ $message }}</div> @enderror
                            <div class="si-status-composer-foot">
                                <span class="si-status-count" id="siStatusCount">{{ mb_strlen($statusText) }} / 500</span>
                                <button type="submit" class="si-set-btn primary" wire:loading.attr="disabled" wire:target="postTextStatus">
                                    <i class="fa fa-paper-plane"></i> Post status
                                </button>
                            </div>
                        </form>
                    @elseif($selectedStatus)
                        <div class="si-pane-head">
                            <button type="button" class="si-pane-back si-mobile-only" wire:click="closeStatusViewer" data-si-back title="Back" aria-label="Back">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            <span class="si-avatar" style="width:36px; height:36px; margin:0;">
                                @if($meAvatarUrl)
                                    <img src="{{ $meAvatarUrl }}" alt="">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name ?: auth()->user()->email, 0, 1)) }}
                                @endif
                            </span>
                            <div>
                                <div class="si-pane-title">My status</div>
                                <div class="si-pane-sub">{{ ucfirst($selectedStatus->type) }} · {{ $selectedStatus->created_at->diffForHumans() }} · expires {{ $selectedStatus->created_at->copy()->addDay()->diffForHumans() }}</div>
                            </div>
                            <div class="si-pane-head-actions">
                                <button type="button" class="si-pane-back" wire:click="deleteStatus({{ $selectedStatus->id }})"
                                        wire:confirm="Delete this status? Customers will no longer see it." title="Delete status" aria-label="Delete status">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button type="button" class="si-pane-back si-desktop-only" wire:click="closeStatusViewer" title="Close" aria-label="Close">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="si-status-body">
                            <div class="si-status-stage" wire:key="si-status-stage-{{ $selectedStatus->id }}">
                                @if($selectedStatus->type === 'photo')
                                    <img src="{{ route('status.media', $selectedStatus->id) }}" alt="Status photo">
                                @elseif($selectedStatus->type === 'video')
                                    <video src="{{ route('status.media', $selectedStatus->id) }}" controls autoplay muted playsinline></video>
                                @else
                                    <div class="si-status-card" style="background: {{ $selectedStatus->background_color ?: '#075E54' }}; color: {{ $selectedStatus->text_color ?: '#fff' }};">{{ $selectedStatus->content }}</div>
                                @endif
                                @if($selectedStatus->type !== 'text' && $selectedStatus->content)
                                    <div class="si-status-caption">{{ $selectedStatus->content }}</div>
                                @endif
                            </div>
                            <div class="si-status-viewers">
                                <div class="si-status-viewers-head"><i class="fa fa-eye" style="color:var(--sm-primary);"></i> Viewed by {{ $selectedStatus->views->count() }}</div>
                                <div class="si-status-viewers-list">
                                    @forelse($selectedStatus->views as $view)
                                        <div class="si-status-viewer" wire:key="si-status-view-{{ $view->id }}">
                                            <span class="si-avatar" style="--si-av: {{ user_avatar_color($view->viewer?->id ?? '?') }}">
                                                @if($view->viewer?->avatar)
                                                    <img src="{{ user_avatar_url($view->viewer) }}" alt="">
                                                @else
                                                    {{ strtoupper(substr($view->viewer?->name ?: ($view->viewer?->email ?? '?'), 0, 1)) }}
                                                @endif
                                            </span>
                                            <span class="si-name">{{ $view->viewer?->name ?: ($view->viewer?->email ?? 'Deleted user') }}</span>
                                            <span class="si-status-viewer-time">{{ $view->viewed_at ? \Carbon\Carbon::parse($view->viewed_at)->diffForHumans(null, true) : '' }}</span>
                                        </div>
                                    @empty
                                        <div class="si-empty" style="padding:40px 12px;">
                                            <i class="fa fa-eye-slash"></i>
                                            <p>No views yet.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="si-empty">
                            <i class="fa fa-circle-notch"></i>
                            <h4>Share status updates</h4>
                            <p>Post a photo, video or text for customers. Updates disappear after 24 hours.</p>
                            <div class="si-set-card-actions" style="justify-content:center;">
                                <button type="button" class="si-set-btn primary" data-si-status-pick><i class="fa fa-image"></i> Photo / video</button>
                                <button type="button" class="si-set-btn" wire:click="openStatusComposer"><i class="fa fa-font"></i> Text</button>
                            </div>
                        </div>
                    @endif
                </div>
            @elseif($inboxTab === 'settings')
                <div class="si-pane">
                    @if($settingsSection === '')
                        <div class="si-empty">
                            <i class="fa fa-cog"></i>
                            <h4>Settings</h4>
                            <p>Business tools, call controls and blocked customers for the support inbox.</p>
                        </div>
                    @else
                        @php
                            [$paneTitle, $paneSub] = match ($settingsSection) {
                                'account' => ['Account', 'Your profile photo, name and phone number'],
                                'tools' => ['Business tools', 'Reach customers in bulk and reply faster'],
                                'quick-replies' => ['Quick replies', 'Saved messages any agent can insert by typing /'],
                                'auto-reply' => ['Auto reply', 'Let customers know you got their message when no one replies in time'],
                                'topup-message' => ['Top-up message', 'Sent to the customer when you add funds to their wallet'],
                                'calls' => ['Calls', 'Control whether customers can call support'],
                                default => ['Blocked', 'Customers barred from placing calls'],
                            };
                        @endphp
                        <div class="si-pane-head">
                            <button type="button" class="si-pane-back" wire:click="setSettingsSection('{{ in_array($settingsSection, ['quick-replies', 'auto-reply', 'topup-message'], true) ? 'tools' : '' }}')"
                                    @unless(in_array($settingsSection, ['quick-replies', 'auto-reply', 'topup-message'], true)) data-si-back @endunless title="Back" aria-label="Back">
                                <i class="fa fa-arrow-left"></i>
                            </button>
                            <div>
                                <div class="si-pane-title">{{ $paneTitle }}</div>
                                <div class="si-pane-sub">{{ $paneSub }}</div>
                            </div>
                        </div>
                        <div class="si-pane-body">
                            <div class="si-pane-inner">
                                @if($settingsSection === 'account')
                                    @php $me = auth()->user(); @endphp
                                    <div class="si-acct-avatar-wrap">
                                        <div class="si-acct-avatar" id="siAcctAvatar">
                                            @if($meAvatarUrl)
                                                <img src="{{ $meAvatarUrl }}" alt="Your profile photo">
                                            @else
                                                {{ strtoupper(substr($me->name ?: $me->email, 0, 1)) }}
                                            @endif
                                            <button type="button" class="si-acct-avatar-btn" data-si-avatar-pick title="Change profile photo" aria-label="Change profile photo">
                                                <i class="fa fa-camera"></i>
                                            </button>
                                        </div>
                                        <div class="si-acct-caption" id="siAcctAvatarCaption">Click the camera to change your photo · JPG or PNG, up to 5 MB</div>
                                    </div>
                                    <form wire:submit.prevent="saveAccountSettings">
                                        <div class="si-field">
                                            <label for="siAcctName">Name</label>
                                            <input id="siAcctName" class="si-field-input" type="text" maxlength="100" wire:model="accountName" autocomplete="name" required>
                                            <div class="si-field-hint">Customers see this name and your photo on your status updates.</div>
                                            @error('accountName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="si-field">
                                            <label for="siAcctPhone">Phone number</label>
                                            <div class="si-phone-row">
                                                <div class="si-phone-cc">
                                                    <span>+</span>
                                                    <input class="si-field-input" type="text" inputmode="numeric" maxlength="5" wire:model="accountCountryCode"
                                                           placeholder="971" aria-label="Country code">
                                                </div>
                                                <input id="siAcctPhone" class="si-field-input" type="tel" maxlength="20" wire:model="accountPhone"
                                                       placeholder="50 123 4567" autocomplete="tel-national">
                                            </div>
                                            @error('accountCountryCode') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                            @error('accountPhone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="si-field">
                                            <label for="siAcctEmail">Email</label>
                                            <input id="siAcctEmail" class="si-field-input" type="email" value="{{ $me->email }}" disabled>
                                            <div class="si-field-hint">Your login email can't be changed here.</div>
                                        </div>
                                        <button type="submit" class="si-set-btn primary" wire:loading.attr="disabled" wire:target="saveAccountSettings">
                                            <i class="fa fa-check"></i> Save changes
                                        </button>
                                        @if($accountSaved)
                                            <span class="si-acct-saved" wire:key="si-acct-saved"><i class="fa fa-check-circle"></i> Saved</span>
                                        @endif
                                    </form>
                                @elseif($settingsSection === 'tools')
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-bullhorn"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">Broadcast</div>
                                            <div class="si-set-card-text">Send one message, with an optional image, to selected customers, every user, or users in chosen cities. Each recipient gets it in their support chat.</div>
                                            <div class="si-set-card-actions">
                                                <button type="button" class="si-set-btn primary" wire:click="openBroadcast">
                                                    <i class="fa fa-paper-plane"></i> New broadcast
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-bolt"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">Quick replies</div>
                                            <div class="si-set-card-text">
                                                Save answers to common questions and insert them by typing <code>/</code> in any chat.
                                                {{ $quickReplies->count() }} saved.
                                            </div>
                                            <div class="si-set-card-actions">
                                                <button type="button" class="si-set-btn" wire:click="setSettingsSection('quick-replies')">
                                                    <i class="fa fa-list"></i> Manage quick replies
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-reply-all"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">
                                                Auto reply <span class="si-pill {{ $autoReplyEnabled ? 'on' : '' }}">{{ $autoReplyEnabled ? 'On' : 'Off' }}</span>
                                            </div>
                                            <div class="si-set-card-text">Automatically thank customers and let them know support will be in touch when no one replies within a set time.</div>
                                            <div class="si-set-card-actions">
                                                <button type="button" class="si-set-btn" wire:click="setSettingsSection('auto-reply')">
                                                    <i class="fa fa-cog"></i> Set up auto reply
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-wallet"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">Top-up message</div>
                                            <div class="si-set-card-text">The message a customer gets in their Support chat when you top up their wallet.</div>
                                            <div class="si-set-card-actions">
                                                <button type="button" class="si-set-btn" wire:click="setSettingsSection('topup-message')">
                                                    <i class="fa fa-pen"></i> Edit top-up message
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-robot"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">AI reply bots</div>
                                            <div class="si-set-card-text">
                                                {{ $availableBots->count() }} active {{ \Illuminate\Support\Str::plural('bot', $availableBots->count()) }} can draft replies from the
                                                <i class="fa fa-magic"></i> button in the chat composer.
                                            </div>
                                            @unless($grokConfigured)
                                                <div class="si-set-note">The Grok API key isn't configured, so reply suggestions are hidden until it's set.</div>
                                            @endunless
                                            <div class="si-set-card-actions">
                                                <a href="{{ route('admin.bots') }}" class="si-set-btn"><i class="fa fa-cog"></i> Manage bots</a>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($settingsSection === 'quick-replies')
                                    <p class="si-set-hint">
                                        Type <code>/</code> in a chat (or pick <strong>Quick replies</strong> from the paperclip menu) to insert one.
                                        Use <code>{name}</code> to fill in the customer's first name.
                                    </p>
                                    <div style="margin-bottom:14px;">
                                        <button type="button" class="si-set-btn primary" wire:click="openQuickReplyForm">
                                            <i class="fa fa-plus"></i> Add quick reply
                                        </button>
                                    </div>
                                    @if($quickReplies->isEmpty())
                                        <div class="si-empty" style="padding:40px 20px;">
                                            <i class="fa fa-bolt"></i>
                                            <h4>No quick replies yet</h4>
                                            <p>Save the answers you send most often, like greetings, payment steps or opening hours.</p>
                                        </div>
                                    @else
                                        <div class="si-qr-list">
                                            @foreach($quickReplies as $quickReply)
                                                <div class="si-qr-row" wire:key="si-qr-row-{{ $quickReply->id }}">
                                                    <span class="si-qr-badge">/{{ $quickReply->shortcut }}</span>
                                                    <div class="si-qr-row-text">{{ $quickReply->message }}</div>
                                                    <div class="si-qr-row-actions">
                                                        <button type="button" class="si-qr-icon-btn" wire:click="openQuickReplyForm({{ $quickReply->id }})" title="Edit" aria-label="Edit /{{ $quickReply->shortcut }}">
                                                            <i class="fa fa-pen"></i>
                                                        </button>
                                                        <button type="button" class="si-qr-icon-btn danger" wire:click="deleteQuickReply({{ $quickReply->id }})"
                                                                wire:confirm="Delete the /{{ $quickReply->shortcut }} quick reply?" title="Delete" aria-label="Delete /{{ $quickReply->shortcut }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @elseif($settingsSection === 'auto-reply')
                                    @php
                                        $delayLabel = fn (int $minutes) => $minutes < 60
                                            ? $minutes . ' ' . \Illuminate\Support\Str::plural('minute', $minutes)
                                            : intdiv($minutes, 60) . ' ' . \Illuminate\Support\Str::plural('hour', intdiv($minutes, 60));
                                    @endphp
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-reply-all"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">Send auto reply</div>
                                            <div class="si-set-card-text">
                                                {{ $autoReplyEnabled
                                                    ? 'On — customers get the message below if no one replies within ' . $delayLabel($autoReplyDelay) . '.'
                                                    : 'Off — customers only hear back when an agent replies.' }}
                                            </div>
                                        </div>
                                        {{-- Keyed on state so the checkbox is rebuilt with the server value after each toggle. --}}
                                        <label class="si-switch" wire:key="si-auto-reply-switch-{{ $autoReplyEnabled ? 'on' : 'off' }}">
                                            <input type="checkbox" @checked($autoReplyEnabled) wire:click="toggleAutoReply" aria-label="Send auto reply">
                                            <span></span>
                                        </label>
                                    </div>
                                    <form class="si-set-card" style="display:block;" wire:submit.prevent="saveAutoReply">
                                        <div class="si-field">
                                            <label for="siAutoReplyDelay">Send when no one has replied for</label>
                                            <select id="siAutoReplyDelay" class="si-field-input" wire:model="autoReplyDelay">
                                                @foreach(\App\Services\SupportAutoReply::DELAYS as $minutes)
                                                    <option value="{{ $minutes }}">{{ $delayLabel($minutes) }}</option>
                                                @endforeach
                                            </select>
                                            @error('autoReplyDelay') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="si-field">
                                            <label for="siAutoReplyMessage">Message</label>
                                            <textarea id="siAutoReplyMessage" class="si-field-input si-field-textarea" rows="4" maxlength="1000"
                                                      wire:model="autoReplyMessage" required></textarea>
                                            <div class="si-field-hint"><code>{name}</code> is replaced with the customer's first name. Customers see it as a message from Support.</div>
                                            @error('autoReplyMessage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <button type="submit" class="si-set-btn primary" wire:loading.attr="disabled" wire:target="saveAutoReply">
                                            <i class="fa fa-check"></i> Save
                                        </button>
                                        @if($autoReplySaved)
                                            <span class="si-acct-saved" wire:key="si-auto-reply-saved"><i class="fa fa-check-circle"></i> Saved</span>
                                        @endif
                                    </form>
                                    <p class="si-set-hint">
                                        A customer gets one auto reply per wait. It isn't sent again until an agent has replied, or 24 hours have passed.
                                        Auto replies are labelled in the chat, and the chat stays unread until an agent opens it.
                                    </p>
                                @elseif($settingsSection === 'topup-message')
                                    <form class="si-set-card" style="display:block;" wire:submit.prevent="saveTopupMessage">
                                        <div class="si-field">
                                            <label for="siTopupMessage">Message</label>
                                            <textarea id="siTopupMessage" class="si-field-input si-field-textarea" rows="13" maxlength="2000"
                                                      wire:model="topupMessage" required></textarea>
                                            <div class="si-field-hint">
                                                <code>{amount}</code> is the amount added, <code>{balance}</code> the new wallet balance and
                                                <code>{name}</code> the customer's first name. Line breaks and emoji are kept exactly as typed.
                                            </div>
                                            @error('topupMessage') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        <button type="submit" class="si-set-btn primary" wire:loading.attr="disabled" wire:target="saveTopupMessage">
                                            <i class="fa fa-check"></i> Save
                                        </button>
                                        <button type="button" class="si-set-btn" wire:click="resetTopupMessage">
                                            <i class="fa fa-undo"></i> Use default
                                        </button>
                                        @if($topupMessageSaved)
                                            <span class="si-acct-saved" wire:key="si-topup-message-saved"><i class="fa fa-check-circle"></i> Saved</span>
                                        @endif
                                    </form>
                                    <p class="si-set-hint">
                                        Sent in the customer's Support chat from the admin who does the top-up, the moment the funds are added.
                                    </p>
                                @elseif($settingsSection === 'calls')
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon {{ $supportCallsBlocked ? 'danger' : '' }}">
                                            <i class="fa {{ $supportCallsBlocked ? 'fa-phone-slash' : 'fa-phone' }}"></i>
                                        </span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">Accept support calls</div>
                                            <div class="si-set-card-text">
                                                {{ $supportCallsBlocked
                                                    ? 'All support calls are paused. Customers can still chat, but cannot call support.'
                                                    : 'Customers can voice and video call support from their chat.' }}
                                            </div>
                                        </div>
                                        {{-- Keyed on state so the checkbox is rebuilt with the server value after each toggle. --}}
                                        <label class="si-switch" wire:key="si-calls-switch-{{ $supportCallsBlocked ? 'off' : 'on' }}">
                                            <input type="checkbox" @checked(!$supportCallsBlocked) wire:click="toggleSupportCallBlock" aria-label="Accept support calls">
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-history"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">Unanswered calls</div>
                                            <div class="si-set-card-text">{{ $unansweredCallsCount }} new missed or declined {{ \Illuminate\Support\Str::plural('call', $unansweredCallsCount) }} from customers.</div>
                                            <div class="si-set-card-actions">
                                                <button type="button" class="si-set-btn" wire:click="setInboxTab('calls')"><i class="fa fa-phone"></i> View calls</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="si-set-card">
                                        <span class="si-set-card-icon"><i class="fa fa-ban"></i></span>
                                        <div class="si-set-card-body">
                                            <div class="si-set-card-title">Block one customer</div>
                                            <div class="si-set-card-text">To stop a single customer from calling, open their chat and choose <strong>Block calls</strong> in the header.</div>
                                            <div class="si-set-card-actions">
                                                <button type="button" class="si-set-btn" wire:click="setSettingsSection('blocked')">
                                                    <i class="fa fa-list"></i> View blocked ({{ $blockedCustomers->count() }})
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="si-set-hint">These customers can't place calls. They can still send chat messages.</p>
                                    @if($blockedCustomers->isEmpty())
                                        <div class="si-empty" style="padding:40px 20px;">
                                            <i class="fa fa-ban"></i>
                                            <h4>No blocked customers</h4>
                                            <p>Customers you block from calling will appear here.</p>
                                        </div>
                                    @else
                                        <div class="si-blocked-list">
                                            @foreach($blockedCustomers as $blocked)
                                                <div class="si-blocked-row" wire:key="si-blocked-{{ $blocked['id'] }}">
                                                    <div class="si-avatar" style="--si-av: {{ user_avatar_color($blocked['id']) }}">
                                                        @if($blocked['avatar'])
                                                            <img src="{{ $blocked['avatar'] }}" alt="">
                                                        @else
                                                            {{ strtoupper(substr($blocked['name'] ?? '?', 0, 1)) }}
                                                        @endif
                                                    </div>
                                                    <div class="si-info">
                                                        <div class="si-name">{{ $blocked['name'] }}</div>
                                                        <div class="si-preview">{{ $blocked['email'] }}</div>
                                                    </div>
                                                    <div class="si-blocked-actions">
                                                        @if($blocked['conversation_id'])
                                                            <button type="button" class="si-set-btn" wire:click="selectConversation({{ $blocked['conversation_id'] }})">
                                                                <i class="fa fa-comments"></i> Chat
                                                            </button>
                                                        @endif
                                                        <button type="button" class="si-set-btn" wire:click="unblockCustomerCalls({{ $blocked['id'] }})"
                                                                wire:loading.attr="disabled" wire:target="unblockCustomerCalls({{ $blocked['id'] }})">
                                                            <i class="fa fa-unlock"></i> Unblock
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @elseif($selectedConversationId && $selectedCustomer)
                <div class="si-head">
                    <div class="si-head-who" style="display:flex; align-items:center;">
                        <button type="button" class="si-pane-back si-mobile-only" wire:click="closeConversation" data-si-back title="Back" aria-label="Back to chats">
                            <i class="fa fa-arrow-left"></i>
                        </button>
                        {{-- Tap the customer to open the "Customer info" panel. --}}
                        <div class="si-head-profile" role="button" tabindex="0" title="Customer info"
                             aria-label="Customer info for {{ $selectedCustomer->name ?: $selectedCustomer->email }}"
                             wire:click="openCustomerInfo" wire:keydown.enter="openCustomerInfo">
                            <div class="si-avatar" style="width:36px; height:36px; margin-right:10px; --si-av: {{ user_avatar_color($selectedCustomer->id) }}">
                                {{ strtoupper(substr($selectedCustomer->name ?: $selectedCustomer->email, 0, 1)) }}
                                @if($selectedCustomer->avatar)
                                    @php $headAvatar = user_avatar_url($selectedCustomer); @endphp
                                    <img src="{{ $headAvatar }}" alt="" wire:key="si-av-{{ md5($headAvatar) }}" wire:ignore onerror="this.style.display='none'">
                                @endif
                            </div>
                            <div class="si-head-text">
                                <div class="si-head-name">
                                    {{ $selectedCustomer->name ?? $selectedCustomer->email }}
                                    @if($selectedCustomerCountry)<span class="si-country-prefix">{{ $selectedCustomerCountry }}</span>@endif
                                </div>
                                @php $headSeen = $selectedCustomer->last_seen_at; @endphp
                                <div class="si-head-mail">
                                    @if($selectedCustomer->isOnline())
                                        <span class="si-head-presence is-online">online</span> ·
                                    @elseif($headSeen)
                                        <span class="si-head-presence" title="{{ $headSeen->format('j M Y, h:i A') }}">last seen {{ $headSeen->isToday() ? 'today' : ($headSeen->isYesterday() ? 'yesterday' : $headSeen->format('j M')) }} at {{ $headSeen->format('h:i A') }}</span> ·
                                    @endif
                                    {{ $selectedCustomer->email }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        // Peer metadata for the WebRTC dialer. webrtc-scripts has a
                        // delegated [data-rtc-call] listener that reads exactly these
                        // attributes and calls window.rtcStartCall — no Livewire
                        // round-trip. data-rtc-support is intentionally absent: this is
                        // a targeted dial to one customer, not a fan-out to all admins.
                        $rtcPeerName = $selectedCustomer->name ?: $selectedCustomer->email;
                        $rtcPeerAvatar = $selectedCustomer->avatar ? user_avatar_url($selectedCustomer) : '';
                        $callsBlocked = (bool) ($selectedCustomer->calls_blocked ?? false);
                        // wire:confirm.prompt: question|expected answer, \n = new line.
                        $deleteChatPrompt = 'Permanently delete the whole chat with ' . str_replace('|', '', $rtcPeerName)
                            . '? Every message, photo, video and voice note is removed for you AND for the customer. This cannot be undone.'
                            . '\n\nType DELETE to confirm.|DELETE';
                    @endphp
                    <div class="si-head-actions">
                        <button type="button" class="si-hbtn" title="Voice call" aria-label="Voice call"
                                data-rtc-call="audio"
                                data-peer-id="{{ $selectedCustomer->id }}"
                                data-peer-name="{{ $rtcPeerName }}"
                                data-peer-avatar="{{ $rtcPeerAvatar }}"
                                data-conversation-id="{{ $selectedConversationId }}">
                            <i class="fa fa-phone"></i>
                        </button>
                        <button type="button" class="si-hbtn" title="Video call" aria-label="Video call"
                                data-rtc-call="video"
                                data-peer-id="{{ $selectedCustomer->id }}"
                                data-peer-name="{{ $rtcPeerName }}"
                                data-peer-avatar="{{ $rtcPeerAvatar }}"
                                data-conversation-id="{{ $selectedConversationId }}">
                            <i class="fa fa-video"></i>
                        </button>
                        <div class="si-head-more">
                            <button type="button" class="si-hbtn" data-si-head-more title="More options" aria-label="More options" aria-haspopup="true">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="si-head-more-menu" role="menu">
                                <button type="button" role="menuitem" wire:click="openWalletTopup">
                                    <i class="fa fa-wallet"></i> Top up wallet
                                </button>
                                <button type="button" role="menuitem" class="{{ $callsBlocked ? '' : 'danger' }}" wire:click="toggleCallBlock">
                                    <i class="fa {{ $callsBlocked ? 'fa-phone' : 'fa-ban' }}"></i>
                                    {{ $callsBlocked ? 'Allow calls again' : 'Block calls' }}
                                </button>
                                <button type="button" role="menuitem" class="danger" wire:click="deleteChatHistory"
                                        wire:confirm.prompt="{{ $deleteChatPrompt }}">
                                    <i class="fa fa-trash"></i> Delete chat history
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @if($callsBlocked)
                    {{-- Block calls lives in the ⋮ menu, so show the state here. --}}
                    <div class="si-head-flag">
                        <i class="fa fa-phone-slash"></i> This customer can't place calls
                    </div>
                @endif

                <div class="si-msgs" id="siMsgs">
                    @php
                        $lastRenderedDate = null;
                        $prevSenderKey = null;
                        $renderDateLabel = function ($date) {
                            $day = \Carbon\Carbon::parse($date)->startOfDay();
                            $today = \Carbon\Carbon::today();
                            if ($day->equalTo($today)) return 'Today';
                            if ($day->equalTo($today->copy()->subDay())) return 'Yesterday';
                            if ($day->gt($today->copy()->subDays(7))) return $day->format('l');
                            if ($day->year === $today->year) return $day->format('j F');
                            return $day->format('j F Y');
                        };
                    @endphp
                    @foreach($conversationMessages as $m)
                        @php
                            $messageDate = \Carbon\Carbon::parse($m['created_at'])->format('Y-m-d');
                            $showDateSeparator = $messageDate !== $lastRenderedDate;
                            $lastRenderedDate = $messageDate;
                            // WhatsApp grouping: a run of messages from one sender sits
                            // close together and only its first bubble has the tail.
                            $senderKey = !empty($m['is_auto_reply']) ? 'auto' : $m['sender_id'];
                            $isGroupStart = $showDateSeparator || $senderKey !== $prevSenderKey;
                            $prevSenderKey = $senderKey;
                            $isMedia = in_array(($m['attachment_type'] ?? ''), ['image', 'video'], true);
                            // Text is the bubble's last line, so the time can share it.
                            $hasText = !empty($m['message']) && !$isMedia && ($m['attachment_type'] ?? '') !== 'wallet_receipt';
                        @endphp
                        @if($showDateSeparator)
                            <div class="si-date-sep" wire:key="si-date-{{ $messageDate }}">
                                <span>{{ $renderDateLabel($m['created_at']) }}</span>
                            </div>
                        @endif
                        <div id="si-message-{{ $m['id'] }}" class="si-msg {{ $m['is_mine'] ? 'sent' : 'recv' }} {{ $isMedia ? 'media' : '' }} {{ $isGroupStart ? 'is-first' : '' }}" wire:key="sim-{{ $m['id'] }}-{{ $m['status'] }}">
                            <div class="si-bub {{ $hasText ? 'has-text' : '' }}">
                                <div class="si-msg-actions">
                                    <button type="button" class="si-msg-actions-trigger" data-si-message-menu aria-label="Message actions" title="Message actions"><i class="fa fa-chevron-down"></i></button>
                                    <div class="si-msg-actions-menu">
                                        <button type="button" data-si-message-action="reply" data-message-id="{{ $m['id'] }}"><i class="fa fa-reply"></i> Reply</button>
                                        <button type="button" data-si-message-action="star" data-message-id="{{ $m['id'] }}"><i class="fa {{ $m['is_starred'] ? 'fa-star' : 'fa-star-o' }}"></i> {{ $m['is_starred'] ? 'Unstar' : 'Star' }}</button>
                                        <button type="button" data-si-message-action="forward" data-message-id="{{ $m['id'] }}"><i class="fa fa-share"></i> Forward</button>
                                        @if($m['is_me'])
                                            <button type="button" class="danger" data-si-message-action="delete" data-message-id="{{ $m['id'] }}"><i class="fa fa-trash"></i> Delete</button>
                                        @endif
                                    </div>
                                </div>
                                @if(!empty($m['is_auto_reply']))
                                    <span class="si-agent si-agent-auto"><i class="fa fa-robot"></i> Auto reply</span>
                                @elseif($m['is_mine'] && !$m['is_me'] && $isGroupStart)
                                    <span class="si-agent">{{ $m['sender_name'] }}</span>
                                @endif
                                @if(!empty($m['reply_to']))
                                    <div class="si-reply-quote" data-si-jump-to="{{ $m['reply_to']['id'] }}" role="button" tabindex="0" aria-label="Jump to replied message">
                                        <strong>{{ $m['reply_to']['sender_name'] }}</strong>
                                        <span>{{ $m['reply_to']['excerpt'] }}</span>
                                    </div>
                                @endif
                                @if(($m['attachment_type'] ?? '') === 'wallet_receipt')
                                    <div class="si-wallet-receipt">
                                        <div class="si-wallet-receipt-title"><i class="fa fa-receipt"></i> Wallet top-up receipt</div>
                                        @foreach(array_slice(explode("\n", (string) $m['message']), 1) as $receiptLine)
                                            <div class="si-wallet-receipt-line">{{ $receiptLine }}</div>
                                        @endforeach
                                    </div>
                                @elseif(($m['attachment_type'] ?? '') === 'call')
                                    @php
                                        // Call rows carry no attachment_url/message, so they need their
                                        // own branch before the media block. Mirrors my-chat's renderer:
                                        // the summary row's sender is always the caller, so `is_mine`
                                        // (sender is an admin) reads as "Outgoing" from this panel.
                                        $callKind   = $m['attachment_mime'] ?? 'audio';
                                        $callStatus = $m['attachment_original_name'] ?? 'ended';
                                        $isMissed   = $callStatus === 'missed' && !$m['is_mine'];
                                        $iconClass  = $callKind === 'video' ? 'fa-video' : 'fa-phone';
                                        // "voice call", as WhatsApp and the chat list say; stored as audio.
                                        $callKindLabel = $callKind === 'video' ? 'video' : 'voice';
                                        $callLabel = match ($callStatus) {
                                            'missed'   => 'Missed ' . $callKindLabel . ' call',
                                            'declined' => ($m['is_mine'] ? 'No answer' : 'Call declined'),
                                            default   => ucfirst($callKindLabel) . ' call',
                                        };
                                    @endphp
                                    <div class="si-call {{ $isMissed ? 'missed' : '' }}">
                                        <span class="si-call-icon"><i class="fa {{ $iconClass }}"></i></span>
                                        <div class="si-call-body">
                                            <div class="si-call-label">{{ $callLabel }}</div>
                                            <div class="si-call-sub">
                                                {{ $m['is_mine'] ? 'Outgoing' : 'Incoming' }}
                                                @if(!empty($m['attachment_duration']))
                                                    · {{ gmdate('i:s', (int) $m['attachment_duration']) }}
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" class="si-call-back" title="Call back" aria-label="Call back"
                                                data-rtc-call="{{ $callKind }}"
                                                data-peer-id="{{ $selectedCustomer->id }}"
                                                data-peer-name="{{ $rtcPeerName }}"
                                                data-peer-avatar="{{ $rtcPeerAvatar }}"
                                                data-conversation-id="{{ $selectedConversationId }}">
                                            <i class="fa {{ $iconClass }}"></i>
                                        </button>
                                    </div>
                                @elseif(!empty($m['attachment_url']))
                                    @if($m['attachment_type'] === 'image')
                                        <img src="{{ $m['attachment_url'] }}" class="si-msg-image" onclick="siOpenLightbox('{{ $m['attachment_url'] }}')">
                                    @elseif($m['attachment_type'] === 'audio')
                                        <div class="si-msg-voice" data-vp-duration="{{ $m['attachment_duration'] ?? 0 }}" data-message-id="{{ $m['id'] }}">
                                            <audio preload="metadata" src="{{ $m['attachment_url'] }}" onloadedmetadata="siFixAudioDuration(this)"></audio>
                                            <button type="button" class="si-vp-play" data-si-vp-play aria-label="Play voice message">
                                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"></path></svg>
                                            </button>
                                            <div class="si-vp-body">
                                                <div class="si-vp-waveform" data-si-vp-waveform>
                                                    @foreach([22, 38, 55, 32, 68, 42, 84, 55, 36, 72, 48, 91, 62, 40, 77, 52, 33, 65, 88, 46, 70, 37, 58, 82, 44, 69, 31, 54, 76, 43, 61, 35] as $barHeight)
                                                        <span class="si-vp-bar" style="height:{{ $barHeight }}%"></span>
                                                    @endforeach
                                                </div>
                                                <div class="si-vp-time" data-si-vp-time>{{ gmdate('i:s', (int) ($m['attachment_duration'] ?? 0)) }}</div>
                                            </div>
                                        </div>
                                    @elseif($m['attachment_type'] === 'video')
                                        <video controls preload="metadata" src="{{ $m['attachment_url'] }}" poster="{{ $m['attachment_thumbnail_url'] ?? '' }}" style="max-width:260px; border-radius:8px;"></video>
                                    @else
                                        <a href="{{ $m['attachment_url'] }}" target="_blank" rel="noopener" class="si-msg-file" download="{{ $m['attachment_original_name'] }}">
                                            <i class="fa fa-file"></i>
                                            <div>
                                                <div class="fname">{{ $m['attachment_original_name'] ?? 'Attachment' }}</div>
                                                @if(!empty($m['attachment_size']))
                                                @if($m['is_starred'])<i class="fa fa-star si-starred" title="Starred"></i>@endif
                                                    <div class="fsize">{{ number_format($m['attachment_size'] / 1024, 1) }} KB</div>
                                                @endif
                                            </div>
                                        </a>
                                    @endif
                                @endif
                                @if(!empty($m['message']) && ($m['attachment_type'] ?? '') !== 'wallet_receipt')
                                    <p>{{ $m['message'] }}</p>
                                @endif
                                <div class="si-btime">
                                    {{ \Carbon\Carbon::parse($m['created_at'])->format('h:i A') }}
                                    @if($m['is_mine'])
                                        @include('livewire.partials._support-ticks', ['status' => $m['status']])
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Customer "typing…" bubble (partials/_chat-typing). Outside the
                     morphing thread, and wire:ignore so re-renders can't hide it. --}}
                <div class="ev-typing-row si-typing-row" id="siTyping" wire:ignore aria-live="polite" aria-label="Customer is typing">
                    <div class="ev-typing-bubble"><span></span><span></span><span></span></div>
                </div>

                @if($replyingToPreview)
                    <div class="si-reply-composer">
                        <div class="si-reply-composer-copy">
                            <strong>Replying to {{ $replyingToPreview['sender_name'] }}</strong>
                            <span>{{ $replyingToPreview['excerpt'] }}</span>
                        </div>
                        <button type="button" wire:click="cancelReply" aria-label="Cancel reply" title="Cancel reply"><i class="fa fa-times"></i></button>
                    </div>
                @endif

                <div class="si-att-preview" id="siAttachPreview">
                    <div class="si-upload-thumb" id="siAttachThumbWrap">
                        <img id="siAttachThumb" alt="Upload preview">
                        <i class="fa fa-file-image-o" id="siAttachThumbIcon"></i>
                    </div>
                    <div class="info">
                        <div class="si-upload-meta">
                            <span id="siAttachPreviewLabel">Preparing upload…</span>
                            <strong id="siAttachProgressText">0%</strong>
                        </div>
                        <div class="si-upload-track"><span id="siAttachProgressBar"></span></div>
                    </div>
                </div>

                @if($suggestedReply !== '')
                    @php $suggestedBy = $availableBots->firstWhere('id', $suggestedByBotId); @endphp
                    <div class="si-suggest-chip" wire:key="suggest-chip-{{ $selectedConversationId }}">
                        <div class="si-suggest-head">
                            <span class="si-suggest-label">
                                <i class="fa fa-magic"></i>
                                Suggested reply{{ $suggestedBy ? ' — ' . $suggestedBy->name : '' }}
                            </span>
                            <div class="si-suggest-actions">
                                <button type="button" wire:click="useSuggestion" title="Insert this into the reply box">
                                    <i class="fa fa-check"></i> Use
                                </button>
                                <button type="button" wire:click="dismissSuggestion" title="Dismiss suggestion">
                                    <i class="fa fa-times"></i> Dismiss
                                </button>
                            </div>
                        </div>
                        <div class="si-suggest-body">{{ $suggestedReply }}</div>
                    </div>
                @endif

                @php
                    $quickReplyName = strtok(trim((string) ($selectedCustomer->name ?: \Illuminate\Support\Str::before((string) $selectedCustomer->email, '@'))), ' ') ?: '';
                @endphp
                <div class="si-input">
                    {{-- Quick-reply picker, opened by typing "/" or from the paperclip menu.
                         wire:ignore keeps its open/highlight state through inbox refreshes;
                         it is rebuilt whenever the thread remounts (e.g. after editing
                         replies in Settings). --}}
                    <div class="si-qr-menu" id="siQrMenu" wire:ignore>
                        <div class="si-qr-menu-head">
                            <i class="fa fa-bolt"></i> Quick replies
                            <small>↑ ↓ to choose · Enter to insert · Esc to close</small>
                        </div>
                        <div class="si-qr-menu-list" role="listbox" aria-label="Quick replies">
                            @foreach($quickReplies as $quickReply)
                                <button type="button" class="si-qr-item" role="option" data-si-qr-item
                                        data-shortcut="{{ $quickReply->shortcut }}" data-message="{{ $quickReply->message }}">
                                    <span class="si-qr-badge">/{{ $quickReply->shortcut }}</span>
                                    <span class="si-qr-item-text">{{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $quickReply->message), 160) }}</span>
                                </button>
                            @endforeach
                            <div class="si-qr-empty" data-si-qr-empty>
                                {{ $quickReplies->isEmpty() ? 'No quick replies saved yet.' : 'No quick replies match.' }}
                            </div>
                        </div>
                        <button type="button" class="si-qr-manage" data-si-qr-manage>
                            <i class="fa fa-cog"></i> {{ $quickReplies->isEmpty() ? 'Add quick replies' : 'Manage quick replies' }}
                        </button>
                    </div>
                    <form wire:submit.prevent="sendReply" data-si-customer-name="{{ $quickReplyName }}" style="display:flex; gap:8px; width:100%; align-items:center;">
                        {{-- Paperclip opens the same menu the customer app has. Each kind
                             has its own hidden input so the OS picker shows the right
                             filter; Location and Contact skip uploads entirely and send
                             a formatted text message. --}}
                        <div class="si-att-wrap">
                            <button type="button" class="si-att-btn" id="siAttachBtn" title="Attach" aria-haspopup="true"><i class="fa fa-paperclip"></i></button>
                            <div class="si-attach-menu" id="siAttachMenu">
                                <button type="button" data-si-attach="photos"><i class="fa fa-image"></i> Photos &amp; Videos</button>
                                <button type="button" data-si-attach="camera"><i class="fa fa-camera"></i> Camera</button>
                                <button type="button" data-si-attach="audio"><i class="fa fa-headphones"></i> Audio</button>
                                <button type="button" data-si-attach="docs"><i class="fa fa-file-alt"></i> Documents</button>
                                <button type="button" data-si-attach="location"><i class="fa fa-map-marker-alt"></i> Location</button>
                                <button type="button" data-si-attach="contact"><i class="fa fa-user"></i> Contact</button>
                                <button type="button" data-si-attach="quick"><i class="fa fa-bolt"></i> Quick replies</button>
                            </div>
                        </div>
                        <input type="file" id="siInputPhotos" accept="image/*,video/*" multiple style="display:none">
                        <input type="file" id="siInputCamera" accept="image/*" capture="environment" style="display:none">
                        <input type="file" id="siInputAudio" accept="audio/*" style="display:none">
                        <input type="file" id="siInputDocs"
                               accept="application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar"
                               style="display:none">

                        <div class="si-rec-bar" id="siRecBar">
                            <button type="button" class="rec-btn delete" id="siRecCancel" title="Discard recording" aria-label="Discard recording">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                            </button>
                            <span class="dot"></span>
                            <span id="siRecTime">0:00</span>
                            <span class="rec-spacer"></span>
                            <button type="button" class="rec-btn" id="siRecPause" title="Pause recording" aria-label="Pause recording">
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"></rect><rect x="14" y="5" width="4" height="14" rx="1"></rect></svg>
                            </button>
                            <button type="button" class="rec-btn" id="siRecRestart" title="Restart recording" aria-label="Restart recording">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            </button>
                            <button type="button" class="rec-btn send" id="siRecSend" title="Send voice message" aria-label="Send voice message">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                            </button>
                        </div>

                        <textarea wire:model="reply" id="siReplyInput" placeholder="Type a reply, or / for quick replies…" rows="1"
                                  data-conversation-id="{{ $selectedConversationId }}"
                                  onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();this.form.requestSubmit();}"></textarea>

                        @if($grokConfigured && $availableBots->isNotEmpty())
                            <div class="si-suggest-wrap">
                                <button type="button" class="si-suggest-btn" id="siSuggestBtn"
                                        title="Draft a reply with an AI bot"
                                        wire:loading.attr="disabled" wire:target="suggestReply">
                                    <span wire:loading.remove wire:target="suggestReply"><i class="fa fa-magic"></i></span>
                                    <span wire:loading wire:target="suggestReply"><i class="fa fa-spinner fa-spin"></i></span>
                                </button>
                                <div class="si-suggest-menu" id="siSuggestMenu">
                                    <div class="si-suggest-menu-head">Draft with…</div>
                                    @foreach($availableBots as $bot)
                                        <button type="button" wire:click="suggestReply({{ $bot->id }})"
                                                data-si-suggest-menu-item>
                                            <strong>{{ $bot->name }}</strong>
                                            @if($bot->description)
                                                <span>{{ $bot->description }}</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <button type="button" class="si-mic-btn" id="siMicBtn" title="Record voice"><i class="fa fa-microphone"></i></button>
                        <button type="submit"><i class="fa fa-paper-plane"></i></button>
                    </form>
                </div>

                @if($customerInfoOpen && $customerInfo)
                    @php
                        $ci = $customerInfo;
                        $ciName = $selectedCustomer->name ?: $selectedCustomer->email;
                        $ciStatus = strtolower((string) ($selectedCustomer->status ?: 'active'));
                    @endphp
                    {{-- Full-pane "Contact info" page over the chat, like my-chat's. --}}
                    <aside class="si-info-panel" aria-label="Contact info" wire:key="si-info-{{ $selectedConversationId }}"
                           x-data x-on:keydown.escape.window="$wire.closeCustomerInfo()">
                        <div class="si-info-head">
                            <button type="button" class="si-info-close" wire:click="closeCustomerInfo" title="Back to chat" aria-label="Back to chat">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                            <h3>Contact info</h3>
                        </div>
                        <div class="si-info-body">
                          <div class="si-info-inner">
                            <div class="si-info-hero">
                                <div class="si-info-avatar" style="--si-av: {{ user_avatar_color($selectedCustomer->id) }}">
                                    {{ strtoupper(substr($ciName, 0, 1)) }}
                                    @if($ci['avatar'])
                                        <a href="{{ $ci['avatar'] }}" target="_blank" rel="noopener" title="Open full photo">
                                            <img src="{{ $ci['avatar'] }}" alt="{{ $ciName }}" onerror="this.closest('a').remove()">
                                        </a>
                                    @endif
                                </div>
                                <div class="si-info-name">{{ $ciName }}</div>
                                @if($selectedCustomer->name)<div class="si-info-mail">{{ $selectedCustomer->email }}</div>@endif
                                @if($ci['online'])
                                    <div class="si-info-seen online">Online</div>
                                @elseif($ci['last_seen'])
                                    <div class="si-info-seen" title="{{ $ci['last_seen']->format('j M Y, h:i A') }}">Last seen {{ $ci['last_seen']->diffForHumans() }}</div>
                                @endif
                                <div class="si-info-chips">
                                    <span class="si-info-chip {{ $ciStatus === 'active' ? 'good' : ($ciStatus === 'pending' ? 'warn' : 'bad') }}">{{ ucfirst($ciStatus) }}</span>
                                    @if($selectedCustomer->verified || $selectedCustomer->email_verified_at)
                                        <span class="si-info-chip good"><i class="fa fa-check-circle"></i> Verified</span>
                                    @else
                                        <span class="si-info-chip warn">Not verified</span>
                                    @endif
                                    @if($ci['plan'] && !$ci['plan']['expired'])
                                        <span class="si-info-chip plan"><i class="fa fa-crown"></i> {{ $ci['plan']['name'] }}</span>
                                    @endif
                                    @if($callsBlocked)
                                        <span class="si-info-chip bad"><i class="fa fa-phone-slash"></i> Calls blocked</span>
                                    @endif
                                </div>

                                <div class="si-info-actions">
                                    <button type="button" class="si-info-action" title="Voice call"
                                            data-rtc-call="audio" data-peer-id="{{ $selectedCustomer->id }}" data-peer-name="{{ $rtcPeerName }}"
                                            data-peer-avatar="{{ $rtcPeerAvatar }}" data-conversation-id="{{ $selectedConversationId }}">
                                        <span class="si-info-action-icon"><i class="fa fa-phone"></i></span><span>Voice</span>
                                    </button>
                                    <button type="button" class="si-info-action" title="Video call"
                                            data-rtc-call="video" data-peer-id="{{ $selectedCustomer->id }}" data-peer-name="{{ $rtcPeerName }}"
                                            data-peer-avatar="{{ $rtcPeerAvatar }}" data-conversation-id="{{ $selectedConversationId }}">
                                        <span class="si-info-action-icon"><i class="fa fa-video"></i></span><span>Video</span>
                                    </button>
                                    <button type="button" class="si-info-action" wire:click="openWalletTopup" title="Top up wallet">
                                        <span class="si-info-action-icon"><i class="fa fa-wallet"></i></span><span>Top up</span>
                                    </button>
                                    <a class="si-info-action" href="{{ route('admin.users', ['id' => $selectedCustomer->id]) }}" target="_blank" rel="noopener" title="Open this user in Users">
                                        <span class="si-info-action-icon"><i class="fa fa-user-cog"></i></span><span>Edit user</span>
                                    </a>
                                </div>
                            </div>

                            <div class="si-info-rows">
                                <button type="button" class="si-info-row" wire:click="openCustomerMedia" data-si-open>
                                    <span class="si-info-row-icon"><i class="fa fa-image"></i></span>
                                    <span class="si-info-row-text">Media, links and docs</span>
                                    <span class="si-info-row-meta">{{ number_format($ci['media_links_docs']) }}</span>
                                    <i class="fa fa-chevron-right si-info-row-go"></i>
                                </button>
                                <button type="button" class="si-info-row" wire:click="openWalletTopup">
                                    <span class="si-info-row-icon"><i class="fa fa-wallet"></i></span>
                                    <span class="si-info-row-text">Wallet balance<small>Top up the customer's wallet</small></span>
                                    <span class="si-info-row-meta strong">${{ number_format($ci['wallet_balance'], 2) }}</span>
                                    <i class="fa fa-chevron-right si-info-row-go"></i>
                                </button>
                            </div>

                            <section class="si-info-section">
                                <h4>Account</h4>
                                <dl class="si-info-list">
                                    <div><dt>User ID</dt><dd>#{{ $selectedCustomer->id }}</dd></div>
                                    @if($selectedCustomer->username)<div><dt>Username</dt><dd>{{ $selectedCustomer->username }}</dd></div>@endif
                                    <div><dt>Email</dt><dd><a href="mailto:{{ $selectedCustomer->email }}">{{ $selectedCustomer->email }}</a></dd></div>
                                    @if($ci['phone'])<div><dt>Phone</dt><dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $ci['phone']) }}">{{ $ci['phone'] }}</a></dd></div>@endif
                                    @if($ci['phone2'])<div><dt>Phone 2</dt><dd><a href="tel:{{ preg_replace('/[^\d+]/', '', $ci['phone2']) }}">{{ $ci['phone2'] }}</a></dd></div>@endif
                                    @if($selectedCustomerCountry)<div><dt>Country</dt><dd>{{ $selectedCustomerCountry }}</dd></div>@endif
                                    @if($ci['city'])<div><dt>City</dt><dd>{{ $ci['city'] }}</dd></div>@endif
                                    @if($ci['account_type'])<div><dt>Account type</dt><dd>{{ $ci['account_type'] }}</dd></div>@endif
                                    <div><dt>Signs in with</dt><dd>{{ $ci['sign_in'] }}</dd></div>
                                    @if($ci['plan'])
                                        <div><dt>Premium</dt><dd>{{ $ci['plan']['name'] }}@if($ci['plan']['expires']) · {{ $ci['plan']['expired'] ? 'expired' : 'until' }} {{ $ci['plan']['expires']->format('j M Y') }}@endif</dd></div>
                                    @endif
                                    <div><dt>Joined</dt><dd>{{ $selectedCustomer->created_at?->format('j M Y') ?? '—' }}</dd></div>
                                    @if($selectedCustomer->registration_country || $selectedCustomer->registration_ip)
                                        <div><dt>Signed up from</dt><dd>{{ collect([$selectedCustomer->registration_country, $selectedCustomer->registration_ip])->filter()->implode(' · ') }}</dd></div>
                                    @endif
                                </dl>
                            </section>

                            <section class="si-info-section">
                                <h4>Support activity</h4>
                                <div class="si-info-stats">
                                    <div class="si-info-stat"><strong>{{ number_format($ci['stats']['messages']) }}</strong><span>Messages sent</span></div>
                                    <div class="si-info-stat"><strong>{{ number_format($ci['stats']['media']) }}</strong><span>Media &amp; files</span></div>
                                    <div class="si-info-stat"><strong>{{ number_format($ci['stats']['calls']) }}</strong><span>Calls</span></div>
                                    <div class="si-info-stat {{ $ci['stats']['missed_calls'] ? 'bad' : '' }}"><strong>{{ number_format($ci['stats']['missed_calls']) }}</strong><span>Missed / declined</span></div>
                                </div>
                                @if($ci['stats']['first_contact'])
                                    <div class="si-info-note">First contacted support {{ \Carbon\Carbon::parse($ci['stats']['first_contact'])->format('j M Y') }}</div>
                                @endif
                            </section>

                            @if(trim((string) $selectedCustomer->about) !== '')
                                <section class="si-info-section">
                                    <h4>About</h4>
                                    <p class="si-info-about">{{ \Illuminate\Support\Str::limit(trim($selectedCustomer->about), 600) }}</p>
                                </section>
                            @endif

                            <section class="si-info-section">
                                <h4>Listings <span>{{ $ci['profiles_total'] }}</span></h4>
                                @if($customerInfoNotice)
                                    <div class="si-info-notice" role="status"><i class="fa fa-check-circle"></i> <span>{{ $customerInfoNotice }}</span></div>
                                @endif
                                @if($ci['profiles_total'] > 5 || $listingSearch !== '')
                                    <div class="si-info-search">
                                        <i class="fa fa-search"></i>
                                        <input type="search" wire:model.live.debounce.300ms="listingSearch" placeholder="Search listings by name, city or ID…"
                                               aria-label="Search this customer's listings" autocomplete="off">
                                    </div>
                                @endif
                                @forelse($ci['profiles'] as $profile)
                                    @php $upgrading = $upgradeProfileId === $profile['id'] && $upgradeOptions; @endphp
                                    <div class="si-info-listing-item {{ $upgrading ? 'is-upgrading' : '' }}" wire:key="si-info-listing-{{ $profile['id'] }}">
                                        <div class="si-info-listing">
                                            <div class="si-info-listing-main">
                                                <span class="si-info-listing-img">
                                                    @if($profile['image'])
                                                        <img src="{{ $profile['image'] }}" alt="" loading="lazy" onerror="this.remove()">
                                                    @else
                                                        <i class="fa fa-image"></i>
                                                    @endif
                                                </span>
                                                <span class="si-info-listing-text">
                                                    <span class="si-info-listing-name" style="display:block;">{{ $profile['name'] }}</span>
                                                    <span class="si-info-listing-sub" style="display:block;">
                                                        {{ collect([$profile['category'], $profile['city']])->filter()->implode(' · ') ?: 'Listing' }} · #{{ $profile['id'] }}
                                                        @if($profile['archived']) · <span style="color:#b45309;">Archived</span>@endif
                                                    </span>
                                                    @if($profile['package_active'])
                                                        <span class="si-info-pkg on"><i class="fa fa-crown"></i> {{ $profile['package'] }}@if($profile['package_until']) · until {{ $profile['package_until']->format('j M Y') }}@endif</span>
                                                    @elseif($profile['package'])
                                                        <span class="si-info-pkg off">{{ $profile['package'] }} · expired {{ $profile['package_until']?->format('j M Y') }}</span>
                                                    @else
                                                        <span class="si-info-pkg">Free listing</span>
                                                    @endif
                                                </span>
                                            </div>
                                            @if($upgrading)
                                                <button type="button" class="si-info-upgrade is-open" wire:click="closeListingUpgrade" title="Close the upgrade form">
                                                    <i class="fa fa-times"></i> Close
                                                </button>
                                            @else
                                                <button type="button" class="si-info-upgrade" wire:click="openListingUpgrade({{ $profile['id'] }})" title="Upgrade {{ $profile['name'] }}">
                                                    <i class="fa fa-arrow-up"></i> Upgrade
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Upgrade form opens right under its listing, on this page. --}}
                                        @if($upgrading)
                                            @php
                                                $uo = $upgradeOptions;
                                                $uoPackage = collect($uo['packages'])->firstWhere('id', (int) $upgradePackageId);
                                                $uoCanCharge = $uo['price'] !== null && $uo['balance'] >= $uo['price'];
                                            @endphp
                                            <form class="si-upgrade-form" wire:submit.prevent="upgradeListing" aria-label="Upgrade {{ $profile['name'] }}">
                                                @if($uo['archived'])
                                                    <div class="si-upgrade-warn">This listing is archived, so it stays hidden until it's reposted.</div>
                                                @endif
                                                @if(empty($uo['packages']))
                                                    <div class="text-danger small mb-2">No packages are set up for this listing's country. Add them in Packages first.</div>
                                                @else
                                                    <span class="si-upgrade-label">Package</span>
                                                    <div class="si-upgrade-options">
                                                        @foreach($uo['packages'] as $pkg)
                                                            <label class="si-upgrade-opt" wire:key="si-up-pkg-{{ $pkg['id'] }}">
                                                                <input type="radio" name="siUpgradePackage" value="{{ $pkg['id'] }}" wire:model.live="upgradePackageId">
                                                                <span>{{ $pkg['name'] }}@if($pkg['tagline'])<small>{{ $pkg['tagline'] }}</small>@endif</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    @error('upgradePackageId') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                                                    <span class="si-upgrade-label">Duration</span>
                                                    <div class="si-upgrade-options">
                                                        @foreach(($uoPackage['tiers'] ?? []) as $tier)
                                                            <label class="si-upgrade-opt" wire:key="si-up-days-{{ $upgradePackageId }}-{{ $tier['days'] }}">
                                                                <input type="radio" name="siUpgradeDays" value="{{ $tier['days'] }}" wire:model.live="upgradeDays">
                                                                <span>{{ $tier['days'] }} days<small>${{ number_format($tier['price'], 2) }}</small></span>
                                                            </label>
                                                        @endforeach
                                                        <label class="si-upgrade-opt" wire:key="si-up-days-custom">
                                                            <input type="radio" name="siUpgradeDays" value="custom" wire:model.live="upgradeDays">
                                                            <span>Custom<small>any number of days</small></span>
                                                        </label>
                                                    </div>
                                                    @if($upgradeDays === 'custom')
                                                        <input type="number" class="si-upgrade-days" min="1" max="3650" step="1" wire:model="upgradeCustomDays" placeholder="Number of days" aria-label="Number of days">
                                                    @endif
                                                    @error('upgradeDays') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                                                    @error('upgradeCustomDays') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                                                    <span class="si-upgrade-label">Payment</span>
                                                    <div class="si-upgrade-pay">
                                                        <label class="si-upgrade-opt">
                                                            <input type="radio" name="siUpgradePayment" value="free" wire:model.live="upgradePayment">
                                                            <span>Free<small>No charge to the customer</small></span>
                                                        </label>
                                                        <label class="si-upgrade-opt">
                                                            <input type="radio" name="siUpgradePayment" value="wallet" wire:model.live="upgradePayment" @disabled(!$uoCanCharge && $upgradePayment !== 'wallet')>
                                                            <span>
                                                                Charge wallet{{ $uo['price'] !== null ? ' $' . number_format($uo['price'], 2) : '' }}
                                                                <small>
                                                                    Balance ${{ number_format($uo['balance'], 2) }}
                                                                    @if($uo['price'] === null) · choose a package duration to charge
                                                                    @elseif(!$uoCanCharge) · not enough balance
                                                                    @endif
                                                                </small>
                                                            </span>
                                                        </label>
                                                    </div>
                                                    @error('upgradePayment') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                                                    <label class="si-upgrade-label" for="siUpgradeMessage">Message to the customer</label>
                                                    <textarea id="siUpgradeMessage" wire:model="upgradeMessage" maxlength="2000"></textarea>
                                                    <div class="si-upgrade-hint">
                                                        Sent in this chat. {name}, {listing}, {package}, {days} and {date} are filled in for you.
                                                        The upgrade email also goes out when it's on in Mail Settings.
                                                    </div>
                                                    @error('upgradeMessage') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                                                @endif

                                                <div class="si-modal-actions">
                                                    <button type="button" class="ghost" wire:click="closeListingUpgrade">Cancel</button>
                                                    <button type="submit" class="primary" @disabled(empty($uo['packages'])) wire:loading.attr="disabled" wire:target="upgradeListing">
                                                        <i class="fa fa-check"></i> Upgrade &amp; send
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                @empty
                                    <div class="si-info-note" style="margin-top:0;">
                                        {{ $listingSearch !== '' ? 'No listings match "' . $listingSearch . '".' : 'No listings.' }}
                                    </div>
                                @endforelse
                                @if($listingSearch === '' && $ci['profiles_total'] > count($ci['profiles']))
                                    <div class="si-info-note">Showing the latest {{ count($ci['profiles']) }} of {{ $ci['profiles_total'] }}. Search to find the others.</div>
                                @elseif($listingSearch !== '' && $ci['profiles_found'] > count($ci['profiles']))
                                    <div class="si-info-note">Showing {{ count($ci['profiles']) }} of {{ $ci['profiles_found'] }} matches. Type more to narrow it down.</div>
                                @endif
                            </section>

                            <div class="si-info-rows">
                                <button type="button" class="si-info-row {{ $callsBlocked ? '' : 'danger' }}" wire:click="toggleCallBlock">
                                    <span class="si-info-row-icon"><i class="fa {{ $callsBlocked ? 'fa-phone' : 'fa-ban' }}"></i></span>
                                    <span class="si-info-row-text">
                                        {{ $callsBlocked ? 'Allow calls again' : 'Block calls' }}
                                        <small>{{ $callsBlocked ? 'This customer can\'t call support right now' : 'Stop this customer from calling support' }}</small>
                                    </span>
                                </button>
                                <button type="button" class="si-info-row danger" wire:click="hideConversation({{ $selectedConversationId }})"
                                        wire:confirm="Delete the chat with {{ str_replace('"', '', $ciName) }} from the Support Inbox? The customer keeps their messages, and the chat comes back here if they write again.">
                                    <span class="si-info-row-icon"><i class="fa fa-trash"></i></span>
                                    <span class="si-info-row-text">Delete chat<small>From the Support Inbox only. The customer keeps their messages.</small></span>
                                </button>
                            </div>
                          </div>
                        </div>
                    </aside>
                @endif
            @else
                <div class="si-empty">
                    <i class="fa fa-comments"></i>
                    <h4>Select a customer</h4>
                    <p>Pick a conversation from the left to start replying.</p>
                </div>
            @endif
        </div>
    </div>

    @if($showBroadcastModal)
        <div class="si-modal open" wire:click.self="closeBroadcast" wire:key="si-broadcast-modal">
            <form class="si-modal-card si-broadcast-card" wire:submit.prevent="sendBroadcast" aria-labelledby="siBroadcastTitle">
                <h5 id="siBroadcastTitle">
                    <i class="fa fa-bullhorn" style="color:var(--sm-primary);"></i>
                    <span>Broadcast message</span>
                    <button type="button" class="si-broadcast-close" wire:click="closeBroadcast" aria-label="Close broadcast dialog" title="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </h5>

                <div class="si-broadcast-audiences">
                    <label>
                        <input type="radio" wire:model.live="broadcastAudience" value="selected">
                        <span><strong>Selected users</strong><br><small>Choose individual accounts</small></span>
                    </label>
                    <label>
                        <input type="radio" wire:model.live="broadcastAudience" value="all">
                        <span><strong>All users</strong><br><small>{{ number_format($broadcastUserCount) }} non-admin accounts</small></span>
                    </label>
                    <label>
                        <input type="radio" wire:model.live="broadcastAudience" value="location">
                        <span><strong>By location</strong><br><small>Choose country and cities</small></span>
                    </label>
                </div>

                @if($broadcastAudience === 'selected')
                    <input type="search" wire:model.live.debounce.250ms="broadcastSearch" placeholder="Search users by name or email">
                    <div class="si-broadcast-count">{{ count($broadcastUserIds) }} selected</div>
                    <div class="si-broadcast-users">
                        @forelse($broadcastUsers as $broadcastUser)
                            <label class="si-broadcast-user" wire:key="si-broadcast-user-{{ $broadcastUser->id }}">
                                <input type="checkbox" wire:model="broadcastUserIds" value="{{ $broadcastUser->id }}">
                                <span>{{ $broadcastUser->name ?: 'Unnamed user' }}<small>{{ $broadcastUser->email }}</small></span>
                            </label>
                        @empty
                            <div class="si-broadcast-count">No users match that search.</div>
                        @endforelse
                    </div>
                    @error('broadcastUserIds') <div class="text-danger small">{{ $message }}</div> @enderror
                @elseif($broadcastAudience === 'all')
                    <div class="si-broadcast-warning">
                        This message will be sent to all {{ number_format($broadcastUserCount) }} non-admin user accounts.
                    </div>
                    <label class="si-broadcast-confirm">
                        <input type="checkbox" wire:model="broadcastAllConfirmed">
                        <span>I understand this will message every eligible user.</span>
                    </label>
                    @error('broadcastAllConfirmed') <div class="text-danger small mb-2">Confirm the all-users broadcast to continue.</div> @enderror
                @else
                    <div class="si-broadcast-location">
                        <label for="siBroadcastCountry">Country</label>
                        <select id="siBroadcastCountry" wire:model.live="broadcastCountryId">
                            <option value="">Choose a country</option>
                            @foreach($broadcastCountries as $country)
                                <option value="{{ $country->id }}">{{ $country->nicename }}</option>
                            @endforeach
                        </select>
                        @error('broadcastCountryId') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                        @if($broadcastCountryId)
                            <label>Cities</label>
                            <div class="si-broadcast-count">
                                {{ count($broadcastCityIds) }} cities selected · {{ number_format($broadcastLocationUserCount) }} eligible users
                            </div>
                            <div class="si-broadcast-users">
                                @forelse($broadcastCities as $city)
                                    <label class="si-broadcast-city" wire:key="si-broadcast-city-{{ $city->id }}">
                                        <input type="checkbox" wire:model="broadcastCityIds" value="{{ $city->id }}">
                                        <span>{{ $city->name }}</span>
                                    </label>
                                @empty
                                    <div class="si-broadcast-count">No cities are available for this country.</div>
                                @endforelse
                            </div>
                            @error('broadcastCityIds') <div class="text-danger small">{{ $message }}</div> @enderror
                        @else
                            <div class="si-broadcast-count">Choose a country to load its cities.</div>
                        @endif
                    </div>
                @endif

                @if($broadcastImage)
                    <div class="si-broadcast-image-preview">
                        <img src="{{ $broadcastImage->temporaryUrl() }}" alt="Image to attach">
                        <span>{{ $broadcastImage->getClientOriginalName() }}</span>
                        <button type="button" wire:click="removeBroadcastImage" title="Remove image" aria-label="Remove image"><i class="fa fa-times"></i></button>
                    </div>
                @endif
                <div class="si-broadcast-tools">
                    <div class="si-broadcast-emoji-wrap">
                        <button type="button" class="si-broadcast-tool" id="siBroadcastEmojiBtn" aria-label="Insert emoji" title="Insert emoji">
                            <span class="si-broadcast-emoji-icon" aria-hidden="true">😊</span> Emoji
                        </button>
                    </div>
                    <label class="si-broadcast-tool" title="Attach an image">
                        <i class="fa fa-image"></i> Image
                        <input type="file" wire:model="broadcastImage" accept="image/*">
                    </label>
                    <span class="text-muted small" wire:loading wire:target="broadcastImage">Uploading image…</span>
                </div>
                <div class="si-broadcast-emoji-menu" id="siBroadcastEmojiMenu" wire:ignore>
                    <input type="search" class="si-broadcast-emoji-search" id="siBroadcastEmojiSearch" placeholder="Search emoji or category" aria-label="Search emoji">
                    <div class="si-broadcast-emoji-categories" role="tablist" aria-label="Emoji categories">
                        <button type="button" class="active" data-broadcast-category="smileys" aria-label="Smileys">😀</button>
                        <button type="button" data-broadcast-category="people" aria-label="People">👋</button>
                        <button type="button" data-broadcast-category="animals" aria-label="Animals and nature">🐻</button>
                        <button type="button" data-broadcast-category="food" aria-label="Food and drink">🍕</button>
                        <button type="button" data-broadcast-category="activities" aria-label="Activities">⚽</button>
                        <button type="button" data-broadcast-category="travel" aria-label="Travel and places">🚗</button>
                        <button type="button" data-broadcast-category="objects" aria-label="Objects">💡</button>
                        <button type="button" data-broadcast-category="symbols" aria-label="Symbols">❤️</button>
                        <button type="button" data-broadcast-category="flags" aria-label="Flags">🏳️</button>
                    </div>
                    <div class="si-broadcast-emoji-grid" id="siBroadcastEmojiGrid" aria-label="Emoji choices"></div>
                </div>
                @error('broadcastImage') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <textarea id="siBroadcastText" wire:model="broadcastText" maxlength="2000" placeholder="Write your message…"></textarea>
                @error('broadcastText') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                <div class="si-modal-actions">
                    <button type="button" class="ghost" wire:click="closeBroadcast">Cancel</button>
                    <button type="submit" class="primary" wire:loading.attr="disabled" wire:target="sendBroadcast">
                        <i class="fa fa-paper-plane"></i> Queue broadcast
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($showWalletTopupModal && $selectedCustomer)
        <div class="si-modal open" wire:click.self="closeWalletTopup" wire:key="si-wallet-topup-modal">
            <form class="si-modal-card" wire:submit.prevent="topUpSelectedUser" aria-labelledby="siWalletTopupTitle">
                <h5 id="siWalletTopupTitle"><i class="fa fa-wallet" style="color:var(--sm-primary);"></i> Top up wallet</h5>
                <div class="text-muted small mb-2">{{ $selectedCustomer->name ?? $selectedCustomer->email }}</div>
                <div class="si-topup-balance">Current balance: <strong>${{ number_format((float) ($selectedCustomer->wallet?->balance ?? 0), 2) }}</strong></div>
                <label class="small font-weight-bold" for="siWalletTopupAmount">Amount ($)</label>
                <input id="siWalletTopupAmount" type="number" min="0.01" max="1000000" step="0.01" wire:model="walletTopupAmount" placeholder="0.00" required>
                @error('walletTopupAmount') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <label class="small font-weight-bold" for="siWalletTopupNote">Note (optional)</label>
                <input id="siWalletTopupNote" type="text" maxlength="255" wire:model="walletTopupNote" placeholder="Admin wallet top-up">
                @error('walletTopupNote') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div class="text-muted small mt-2">
                    Sends your top-up message in this chat — change it in Settings → Business tools → Top-up message.
                </div>
                <div class="si-modal-actions">
                    <button type="button" class="ghost" wire:click="closeWalletTopup">Cancel</button>
                    <button type="submit" class="primary" wire:loading.attr="disabled" wire:target="topUpSelectedUser">
                        <i class="fa fa-check"></i> Save top-up
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($showQuickReplyForm)
        <div class="si-modal open" wire:click.self="closeQuickReplyForm" wire:key="si-quick-reply-modal">
            <form class="si-modal-card si-qr-card" wire:submit.prevent="saveQuickReply" aria-labelledby="siQuickReplyTitle">
                <h5 id="siQuickReplyTitle">
                    <i class="fa fa-bolt" style="color:var(--sm-primary);"></i>
                    {{ $editingQuickReplyId ? 'Edit quick reply' : 'Add quick reply' }}
                </h5>
                <label for="siQuickReplyShortcut">Shortcut</label>
                <div class="si-qr-shortcut-field">
                    <span>/</span>
                    <input id="siQuickReplyShortcut" type="text" maxlength="31" wire:model="quickReplyShortcut"
                           placeholder="thanks" autocomplete="off" style="padding-left:24px;" required>
                </div>
                @error('quickReplyShortcut') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <label for="siQuickReplyMessage">Message</label>
                <textarea id="siQuickReplyMessage" maxlength="2000" wire:model="quickReplyMessage"
                          placeholder="Hi {name}, thanks for reaching out! How can we help today?" required></textarea>
                <div class="si-qr-card-hint"><code>{name}</code> is replaced with the customer's first name.</div>
                @error('quickReplyMessage') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div class="si-modal-actions">
                    <button type="button" class="ghost" wire:click="closeQuickReplyForm">Cancel</button>
                    <button type="submit" class="primary" wire:loading.attr="disabled" wire:target="saveQuickReply">
                        <i class="fa fa-check"></i> Save
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($showForwardModal)
        <div class="si-modal open" wire:click.self="closeForward" wire:key="si-forward-modal">
            <form class="si-modal-card si-forward-card" wire:submit.prevent="forwardSelectedMessage" aria-labelledby="siForwardTitle">
                <h5 id="siForwardTitle"><i class="fa fa-share" style="color:var(--sm-primary);"></i> Forward message</h5>
                <input type="search" wire:model.live.debounce.250ms="forwardSearch" placeholder="Search support chats">
                <div class="si-broadcast-count">{{ count($forwardConversationIds) }} of 5 chats selected</div>
                <div class="si-forward-targets">
                    @forelse($forwardTargets as $target)
                        <label class="si-forward-target" wire:key="si-forward-target-{{ $target->id }}">
                            <input type="checkbox" wire:model="forwardConversationIds" value="{{ $target->id }}"
                                   @disabled(count($forwardConversationIds) >= 5 && !in_array($target->id, $forwardConversationIds))>
                            <span>{{ $target->userOne?->name ?? $target->userOne?->email ?? 'Unknown user' }}<small>{{ $target->userOne?->email }}</small></span>
                        </label>
                    @empty
                        <div class="si-broadcast-count">No other support chats found.</div>
                    @endforelse
                </div>
                @error('forwardConversationIds') <div class="text-danger small mb-2">{{ $message }}</div> @enderror
                <div class="si-modal-actions">
                    <button type="button" class="ghost" wire:click="closeForward">Cancel</button>
                    <button type="submit" class="primary" wire:loading.attr="disabled" wire:target="forwardSelectedMessage"><i class="fa fa-share"></i> Forward</button>
                </div>
            </form>
        </div>
    @endif

    {{-- Page-scope pickers so the Status / Account buttons work from any pane. --}}
    <input type="file" id="siStatusMediaInput" accept="image/*,video/*" hidden>
    <input type="file" id="siAvatarInput" accept="image/*" hidden>

    {{-- Contact-share sheet — same shape as the customer app's: two fields,
         sent as a formatted text message rather than an attachment row. --}}
    <div class="si-modal" id="siContactOverlay">
        <div class="si-modal-card">
            <h5><i class="fa fa-user" style="color: var(--sm-primary);"></i> Share a contact</h5>
            <input type="text" id="siContactName" placeholder="Name">
            <input type="text" id="siContactPhone" placeholder="Phone number">
            <div class="si-modal-actions">
                <button type="button" class="ghost" onclick="siCloseContact()">Cancel</button>
                <button type="button" class="primary" onclick="siSubmitContact()">Send</button>
            </div>
        </div>
    </div>
</div>

@push('js')
@include('livewire.partials._chat-typing')
<script>
window.__siState = window.__siState || { recorder: null, wired: false, recStartedAt: 0, recPausedAt: 0, recPausedElapsed: 0, recTicker: null, voicePlayersWired: false };

window.__siBroadcastEmojiGroups = window.__siBroadcastEmojiGroups || {
    smileys: '😀 😃 😄 😁 😆 😅 😂 🤣 🥲 ☺️ 😊 😇 🙂 🙃 😉 😌 😍 🥰 😘 😗 😙 😚 😋 😛 😝 😜 🤪 🤨 🧐 🤓 😎 🥸 🤩 🥳 🙂‍↕️ 😏 😒 🙂‍↔️ 😞 😔 😟 😕 🙁 ☹️ 😣 😖 😫 😩 🥺 😢 😭 😤 😠 😡 🤬 🤯 😳 🥵 🥶 😱 😨 😰 😥 😓 🤗 🤔 🫣 🤭 🫢 🫡 🤫 🫠 🤥 😶 😶‍🌫️ 😐 😑 😬 🫨 🙄 😯 😦 😧 😮 😲 🥱 😴 🤤 😪 😵 😵‍💫 🤐 🥴 🤢 🤮 🤧 😷 🤒 🤕 🤑 🤠 😈 👿 👹 👺 🤡 💩 👻 💀 ☠️ 👽 👾 🤖 🎃 😺 😸 😹 😻 😼 😽 🙀 😿 😾',
    people: '👋 🤚 🖐️ ✋ 🖖 🫱 🫲 🫳 🫴 👌 🤌 🤏 ✌️ 🤞 🫰 🤟 🤘 🤙 👈 👉 👆 🖕 👇 ☝️ 🫵 👍 👎 ✊ 👊 🤛 🤜 👏 👐 🙌 🤲 🙏 🤝 🙏🏻 💪 🦾 🦿 🦵 🦶 👂 🦻 👃 🧠 🫀 🫁 🦷 🦴 👀 👁️ 👅 👄 🫦 👶 🧒 👦 👧 🧑 👱 👨 🧔 👩 🧓 👴 👵 🙍 🙎 🙅 🙆 💁 🙋 🧏 🙇 🤦 🤷 👮 🕵️ 💂 🥷 👷 🫅 🤴 👸 👳 👲 🧕 🤵 👰 🤰 🫃 🫄 🤱 👼 🎅 🤶 🧑‍🎄 🦸 🦹 🧙 🧚 🧛 🧜 🧝 🧞 🧟 💆 💇 🚶 🧍 🧎 🏃 💃 🕺 🕴️ 👯 🧖 🧗 🤸 🏌️ 🏄 🚣 🏊 ⛹️ 🏋️ 🚴 🚵 🤼 🤽 🤾 🤹 🧘 🛀 🛌 👭 👫 👬 💏 💑 👪',
    animals: '🐵 🙈 🙉 🙊 🐒 🦍 🦧 🐶 🐕 🦮 🐩 🐺 🦊 🦝 🐱 🐈 🦁 🐯 🐅 🐆 🐴 🫎 🫏 🐎 🦄 🦓 🦌 🦬 🐮 🐂 🐃 🐄 🐷 🐖 🐗 🐽 🐏 🐑 🐐 🐪 🐫 🦙 🦒 🐘 🦣 🦏 🦛 🐭 🐁 🐀 🐹 🐰 🐇 🐿️ 🦫 🦔 🦇 🐻 🐻‍❄️ 🐨 🐼 🦥 🦦 🦨 🦘 🦡 🐾 🦃 🐔 🐓 🐣 🐤 🐥 🐦 🐧 🕊️ 🦅 🦆 🦢 🦉 🦤 🪶 🦩 🦚 🦜 🪽 🐸 🐊 🐢 🦎 🐍 🐲 🐉 🦕 🦖 🐳 🐋 🐬 🦭 🐟 🐠 🐡 🦈 🐙 🐚 🪸 🪼 🦀 🦞 🦐 🦑 🦪 🐌 🦋 🐛 🐜 🐝 🪲 🐞 🦗 🪳 🕷️ 🕸️ 🦂 🦟 🪰 🪱 🦠 💐 🌸 💮 🪷 🏵️ 🌹 🥀 🌺 🌻 🌼 🌷 🪻 🌱 🪴 🌲 🌳 🌴 🌵 🌾 🌿 ☘️ 🍀 🍁 🍂 🍃 🪹 🪺 🍄 🌰',
    food: '🍇 🍈 🍉 🍊 🍋 🍋‍🟩 🍌 🍍 🥭 🍎 🍏 🍐 🍑 🍒 🍓 🫐 🥝 🍅 🫒 🥥 🥑 🍆 🥔 🥕 🌽 🌶️ 🫑 🥒 🥬 🥦 🧄 🧅 🥜 🫘 🌰 🍞 🥐 🥖 🫓 🥨 🥯 🥞 🧇 🧀 🍖 🍗 🥩 🥓 🍔 🍟 🍕 🌭 🥪 🌮 🌯 🫔 🥙 🧆 🥚 🍳 🥘 🍲 🫕 🥣 🥗 🍿 🧈 🧂 🥫 🍱 🍘 🍙 🍚 🍛 🍜 🍝 🍠 🍢 🍣 🍤 🍥 🥮 🍡 🥟 🥠 🥡 🦪 🍦 🍧 🍨 🍩 🍪 🎂 🍰 🧁 🥧 🍫 🍬 🍭 🍮 🍯 🍼 🥛 ☕ 🫖 🍵 🍶 🍾 🍷 🍸 🍹 🍺 🍻 🥂 🥃 🫗 🥤 🧋 🧃 🧉 🧊',
    activities: '⚽ 🏀 🏈 ⚾ 🥎 🎾 🏐 🏉 🥏 🎱 🪀 🏓 🏸 🏒 🏑 🥍 🏏 🪃 🥅 ⛳ 🪁 🏹 🎣 🤿 🥊 🥋 🎽 🛹 🛼 🛷 ⛸️ 🥌 🎿 ⛷️ 🏂 🪂 🏋️ 🤼 🤸 ⛹️ 🤺 🤾 🏌️ 🏇 🧘 🧗 🏄 🏊 🤽 🚣 🏆 🥇 🥈 🥉 🏅 🎖️ 🏵️ 🎗️ 🎫 🎟️ 🎪 🤹 🎭 🩰 🎨 🎬 🎤 🎧 🎼 🎹 🥁 🪘 🎷 🎺 🪗 🎸 🪕 🎻 🪈 🎲 ♟️ 🎯 🎳 🎮 🎰 🧩',
    travel: '🚗 🚕 🚙 🚌 🚎 🏎️ 🚓 🚑 🚒 🚐 🛻 🚚 🚛 🚜 🦯 🦽 🦼 🛴 🚲 🛵 🏍️ 🛺 🚨 🚔 🚍 🚘 🚖 🚡 🚠 🚟 🚃 🚋 🚞 🚝 🚄 🚅 🚈 🚂 🚆 🚇 🚊 🚉 ✈️ 🛫 🛬 🛩️ 💺 🛰️ 🚀 🛸 🚁 🛶 ⛵ 🚤 🛥️ 🛳️ ⛴️ 🚢 ⚓ 🛟 ⛽ 🚧 🚦 🚥 🗺️ 🗿 🗽 🗼 🏰 🏯 🏟️ 🎡 🎢 🎠 ⛲ ⛱️ 🏖️ 🏝️ 🏜️ 🌋 ⛰️ 🏔️ 🗻 🏕️ ⛺ 🛖 🏠 🏡 🏘️ 🏚️ 🏗️ 🏭 🏢 🏬 🏣 🏤 🏥 🏦 🏨 🏩 🏪 🏫 🏛️ ⛪ 🕌 🛕 🕍 ⛩️ 🕋 ⛅ 🌤️ 🌥️ 🌦️ 🌧️ ⛈️ 🌩️ 🌨️ ☀️ 🌈 🌪️ 🌫️ 🌊',
    objects: '⌚ 📱 📲 💻 ⌨️ 🖥️ 🖨️ 🖱️ 🖲️ 🕹️ 💽 💾 💿 📀 📼 📷 📸 📹 🎥 📽️ 🎞️ 📞 ☎️ 📟 📠 📺 📻 🎙️ 🎚️ 🎛️ 🧭 ⏱️ ⏲️ ⏰ 🕰️ ⌛ ⏳ 📡 🔋 🪫 🔌 💡 🔦 🕯️ 🪔 🧯 🛢️ 💸 💵 💴 💶 💷 🪙 💰 💳 🧾 💎 ⚖️ 🪜 🧰 🪛 🔧 🔨 ⚒️ 🛠️ ⛏️ 🪚 🔩 ⚙️ 🪤 🧱 ⛓️ 🧲 🔫 💣 🧨 🪓 🔪 🗡️ ⚔️ 🛡️ 🚬 ⚰️ 🪦 ⚱️ 🏺 🔮 📿 🧿 🪬 💈 ⚗️ 🔭 🔬 🕳️ 🩹 🩺 🩻 🩼 💊 💉 🩸 🧬 🦠 🧫 🧪 🌡️ 🧹 🪠 🧺 🧻 🚽 🚰 🚿 🛁 🛀 🧼 🪥 🪒 🧽 🪣 🧴 🛎️ 🔑 🗝️ 🚪 🪑 🛋️ 🛏️ 🛌 🧸 🪆 🖼️ 🪞 🪟 🛍️ 🛒 🎁 🎈 🎏 🎀 🪄 🪅 🎊 🎉 🪩 🎎 🏮 🎐 🧧 ✉️ 📩 📨 📧 💌 📥 📤 📦 🏷️ 📪 📫 📬 📭 📮 📯 📜 📃 📄 📑 🧾 📊 📈 📉 📇 🗃️ 🗄️ 📋 📁 📂 🗂️ 🗞️ 📰 📓 📔 📒 📕 📗 📘 📙 📚 📖 🔖 🧷 🔗 📎 🖇️ 📐 📏 🧮 📌 📍 ✂️ 🖊️ 🖋️ ✒️ 🖌️ 🖍️ 📝 ✏️ 🔍 🔎 🔏 🔐 🔒 🔓',
    symbols: '❤️ 🧡 💛 💚 💙 🩵 💜 🤎 🖤 🩶 🤍 💔 ❤️‍🔥 ❤️‍🩹 ❣️ 💕 💞 💓 💗 💖 💘 💝 💟 ☮️ ✝️ ☪️ 🕉️ ☸️ ✡️ 🔯 🕎 ☯️ ☦️ 🛐 ⛎ ♈ ♉ ♊ ♋ ♌ ♍ ♎ ♏ ♐ ♑ ♒ ♓ 🆔 ⚛️ 🉑 ☢️ ☣️ 📴 📳 🈶 🈚 🈸 🈺 🈷️ ✴️ 🆚 💮 🉐 ㊙️ ㊗️ 🈴 🈵 🈹 🈲 🅰️ 🅱️ 🆎 🆑 🅾️ 🆘 ❌ ⭕ 🛑 ⛔ 📛 🚫 💯 💢 ♨️ 🚷 🚯 🚳 🚱 🔞 📵 🚭 ❗ ❕ ❓ ❔ ‼️ ⁉️ 🔅 🔆〽️ ⚠️ 🚸 🔱 ⚜️ 🔰 ♻️ ✅ 🈯 💹 ❇️ ✳️ ❎ 🌐 💠 Ⓜ️ 🌀 💤 🏧 🚾 ♿ 🅿️ 🛗 🈳 🈂️ 🛂 🛃 🛄 🛅 🚰 🚹 🚺 🚻 🚼 🚮 🎦 📶 🈁 🔣 ℹ️ 🔤 🔡 🔠 🆖 🆗 🆙 🆒 🆕 🆓 🔟 🔢 ▶️ ⏸️ ⏯️ ⏹️ ⏺️ ⏭️ ⏮️ ⏩ ⏪ 🔀 🔁 🔂 ⏫ ⏬ ⬆️ ⬇️ ⬅️ ➡️ ↗️ ↘️ ↙️ ↖️ ↕️ ↔️ 🔄 ↪️ ↩️ ⤴️ ⤵️ 🔃 🔚 🔙 🔛 🔜 🔝 🛐 🔙 🔚 🔛 🔜 🔝 🔺 🔻 🔸 🔹 🔶 🔷 🔳 🔲 ▪️ ▫️ ◾ ◽ ◼️ ◻️ 🟥 🟧 🟨 🟩 🟦 🟪 ⬛ ⬜ 🟫 🔴 🟠 🟡 🟢 🔵 🟣 ⚫ ⚪ 🟤 🏁 🚩 🎌 🏴 🏳️ 🏳️‍🌈 🏳️‍⚧️ 🏴‍☠️',
    flags: '🏁 🚩 🎌 🏴 🏳️ 🏳️‍🌈 🏳️‍⚧️ 🏴‍☠️ 🇦🇪 🇦🇫 🇦🇱 🇩🇿 🇦🇷 🇦🇺 🇦🇹 🇧🇩 🇧🇪 🇧🇷 🇨🇦 🇨🇱 🇨🇳 🇨🇴 🇩🇰 🇪🇬 🇫🇮 🇫🇷 🇩🇪 🇬🇭 🇬🇷 🇭🇰 🇮🇳 🇮🇩 🇮🇷 🇮🇶 🇮🇪 🇮🇹 🇯🇵 🇰🇪 🇰🇼 🇱🇧 🇱🇾 🇲🇾 🇲🇽 🇲🇦 🇳🇵 🇳🇱 🇳🇿 🇳🇬 🇳🇴 🇴🇲 🇵🇰 🇵🇸 🇵🇭 🇵🇱 🇵🇹 🇶🇦 🇷🇴 🇷🇺 🇸🇦 🇸🇬 🇿🇦 🇰🇷 🇪🇸 🇱🇰 🇸🇩 🇸🇪 🇨🇭 🇸🇾 🇹🇼 🇹🇿 🇹🇭 🇹🇳 🇹🇷 🇺🇬 🇺🇦 🇬🇧 🇺🇸 🇺🇾 🇻🇳 🇾🇪 🇿🇲 🇿🇼'
};

window.__siState.emojiCategory = window.__siState.emojiCategory || 'smileys';

document.addEventListener('DOMContentLoaded', () => { siInit(); siWireMedia(); siInitVoicePlayers(); siTrackViewport(); });
document.addEventListener('livewire:navigated', () => { siInit(); siWireMedia(); siInitVoicePlayers(); siTrackViewport(); });
document.addEventListener('livewire:initialized', () => {
    Livewire.on('message-received', () => { siScrollBottom(); siInitVoicePlayers(); });
    // Media tab → chat. Wait past message-received's scroll-to-bottom, then
    // pin the tapped message in view.
    Livewire.on('si-jump-to-message', (payload) => {
        const messageId = payload?.messageId ?? payload?.[0]?.messageId;
        if (messageId) setTimeout(() => siJumpToMessageWhenReady(messageId), 250);
    });
    if (!window.__siState.voicePlayerHooked) {
        window.__siState.voicePlayerHooked = true;
        Livewire.hook('morph.updated', () => siInitVoicePlayers());
    }
});

function siWireMedia() {
    siInitBroadcastEmojiPicker();
    siInitVoicePlayers();
    const componentRoot = document.querySelector('[wire\\:id]');
    if (componentRoot && !window.__siState.emojiObserver && window.MutationObserver) {
        window.__siState.emojiObserver = new MutationObserver(() => siInitBroadcastEmojiPicker());
        window.__siState.emojiObserver.observe(componentRoot, { childList: true, subtree: true });
    }
    if (window.__siState.wired) return;
    window.__siState.wired = true;

    document.addEventListener('click', (e) => {
        // Row chevron menus handle their own clicks (siRowMenu). This listener
        // runs in the capture phase, before them — and their row is
        // [data-si-open], which would flip the phone layout to the chat pane.
        if (e.target.closest('.si-row-actions')) return;
        document.querySelectorAll('.si-row-actions.open').forEach(menu => menu.classList.remove('open'));

        // Media / Settings / the phone back arrow unmount the composer — drop
        // any half-recorded note rather than letting it upload to a thread the
        // admin has left.
        if (e.target.closest('[data-si-tab], [data-si-back]') && window.__siState.recorder) {
            siCancelRecording();
        }

        // Phone layout: flip panes on tap instead of after the round trip.
        // wire:click still runs; its render confirms the same state.
        if (e.target.closest('[data-si-back]')) siSetPane(false);
        else if (e.target.closest('[data-si-open]')) siSetPane(true);

        const headMore = e.target.closest('[data-si-head-more]');
        if (headMore) {
            e.preventDefault();
            e.stopPropagation();
            headMore.closest('.si-head-more')?.classList.toggle('open');
            return;
        }
        document.querySelectorAll('.si-head-more.open').forEach(menu => menu.classList.remove('open'));

        if (e.target.closest('[data-si-status-pick]')) {
            e.preventDefault();
            document.getElementById('siStatusMediaInput')?.click();
            return;
        }
        if (e.target.closest('[data-si-avatar-pick]')) {
            e.preventDefault();
            document.getElementById('siAvatarInput')?.click();
            return;
        }

        const quickReplyItem = e.target.closest('[data-si-qr-item]');
        if (quickReplyItem) {
            e.preventDefault();
            siQrApply(quickReplyItem);
            return;
        }
        if (e.target.closest('[data-si-qr-manage]')) {
            e.preventDefault();
            siQrClose();
            const root = document.querySelector('[wire\\:id]');
            root && Livewire.find(root.getAttribute('wire:id'))?.call('setSettingsSection', 'quick-replies');
            return;
        }
        if (siQrIsOpen() && !e.target.closest('#siQrMenu') && !e.target.closest('#siReplyInput')
            && !e.target.closest('[data-si-attach="quick"]')) {
            siQrClose();
        }

        const voiceButton = e.target.closest('[data-si-vp-play]');
        if (voiceButton) {
            e.preventDefault();
            siToggleVoicePlayback(voiceButton);
            return;
        }

        const replyQuote = e.target.closest('[data-si-jump-to]');
        if (replyQuote) {
            e.preventDefault();
            siJumpToMessage(replyQuote.dataset.siJumpTo);
            return;
        }

        const messageMenuButton = e.target.closest('[data-si-message-menu]');
        if (messageMenuButton) {
            e.preventDefault();
            e.stopPropagation();
            const menu = messageMenuButton.closest('.si-msg-actions');
            document.querySelectorAll('.si-msg-actions.open').forEach(item => {
                if (item !== menu) item.classList.remove('open');
            });
            menu?.classList.toggle('open');
            return;
        }

        const messageAction = e.target.closest('[data-si-message-action]');
        if (messageAction) {
            e.preventDefault();
            e.stopPropagation();
            messageAction.closest('.si-msg-actions')?.classList.remove('open');
            const componentRoot = document.querySelector('[wire\\:id]');
            const component = componentRoot && Livewire.find(componentRoot.getAttribute('wire:id'));
            const messageId = Number(messageAction.dataset.messageId);
            const action = messageAction.dataset.siMessageAction;

            if (action === 'delete' && !window.confirm('Delete this message?')) return;
            if (action === 'reply') {
                component?.call('startReply', messageId);
                siJumpToMessage(messageId);
                setTimeout(() => document.getElementById('siReplyInput')?.focus(), 250);
            } else if (action === 'forward') {
                component?.call('startForward', messageId);
            } else if (action === 'star') {
                component?.call('toggleStar', messageId);
            } else if (action === 'delete') {
                component?.call('deleteMessage', messageId);
            }
            return;
        }

        if (!e.target.closest('.si-msg-actions')) {
            document.querySelectorAll('.si-msg-actions.open').forEach(item => item.classList.remove('open'));
        }

        if (e.target.closest('#siBroadcastEmojiBtn')) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('siBroadcastEmojiMenu')?.classList.toggle('open');
            return;
        }
        const categoryButton = e.target.closest('[data-broadcast-category]');
        if (categoryButton) {
            e.preventDefault();
            window.__siState.emojiCategory = categoryButton.dataset.broadcastCategory;
            const search = document.getElementById('siBroadcastEmojiSearch');
            if (search) search.value = '';
            document.querySelectorAll('[data-broadcast-category]').forEach(button => {
                button.classList.toggle('active', button === categoryButton);
            });
            siRenderBroadcastEmojis();
            return;
        }
        const emojiButton = e.target.closest('[data-broadcast-emoji]');
        if (emojiButton) {
            e.preventDefault();
            siInsertBroadcastEmoji(emojiButton.dataset.broadcastEmoji);
            return;
        }
        if (!e.target.closest('#siBroadcastEmojiMenu')) {
            document.getElementById('siBroadcastEmojiMenu')?.classList.remove('open');
        }
        if (e.target.closest('#siAttachBtn')) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('siAttachMenu')?.classList.toggle('open');
            return;
        }
        const kindBtn = e.target.closest('.si-attach-menu [data-si-attach]');
        if (kindBtn) {
            e.preventDefault();
            document.getElementById('siAttachMenu')?.classList.remove('open');
            siHandleAttachKind(kindBtn.getAttribute('data-si-attach'));
            return;
        }
        // Click anywhere else dismisses the menu.
        if (!e.target.closest('#siAttachMenu') && !e.target.closest('#siAttachBtn')) {
            document.getElementById('siAttachMenu')?.classList.remove('open');
        }
        // AI suggest bot picker — same open/close pattern as the attach menu.
        if (e.target.closest('#siSuggestBtn')) {
            e.preventDefault();
            e.stopPropagation();
            e.target.closest('.si-suggest-wrap')?.classList.toggle('open');
            return;
        }
        if (e.target.closest('[data-si-suggest-menu-item]')) {
            // Picking a bot fires the Livewire suggestReply via wire:click;
            // close the menu immediately so the user sees the loading state.
            document.querySelector('.si-suggest-wrap.open')?.classList.remove('open');
        }
        if (!e.target.closest('.si-suggest-wrap')) {
            document.querySelector('.si-suggest-wrap.open')?.classList.remove('open');
        }
        if (e.target.closest('#siRecCancel')) {
            e.preventDefault();
            siCancelRecording();
            return;
        }
        if (e.target.closest('#siRecPause')) {
            e.preventDefault();
            siTogglePauseRecording();
            return;
        }
        if (e.target.closest('#siRecRestart')) {
            e.preventDefault();
            siRestartRecording();
            return;
        }
        if (e.target.closest('#siRecSend')) {
            e.preventDefault();
            siStopRecording();
            return;
        }
        if (e.target.closest('#siMicBtn')) {
            e.preventDefault();
            if (window.__siState.recorder) siStopRecording();
            else siStartRecording();
            return;
        }
    }, true);

    document.addEventListener('pointerdown', (e) => {
        const waveform = e.target.closest('[data-si-vp-waveform]');
        if (!waveform) return;
        e.preventDefault();
        siSeekVoice(waveform, e.clientX);
        const move = event => siSeekVoice(waveform, event.clientX);
        const up = () => {
            document.removeEventListener('pointermove', move);
            document.removeEventListener('pointerup', up);
        };
        document.addEventListener('pointermove', move);
        document.addEventListener('pointerup', up);
    });

    document.addEventListener('keydown', (e) => {
        const quote = e.target.closest?.('[data-si-jump-to]');
        if (quote && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            siJumpToMessage(quote.dataset.siJumpTo);
        }
    });

    document.addEventListener('input', (e) => {
        if (e.target.matches?.('#siBroadcastEmojiSearch')) siRenderBroadcastEmojis(e.target.value);
        if (e.target.matches?.('#siReplyInput')) siQrSync(e.target);
        if (e.target.matches?.('#siStatusText')) {
            const counter = document.getElementById('siStatusCount');
            if (counter) counter.textContent = e.target.value.length + ' / 500';
        }
    });

    document.addEventListener('change', (e) => {
        const input = e.target;
        if (!input.matches?.('#siStatusMediaInput, #siAvatarInput')) return;
        const file = input.files?.[0];
        input.value = ''; // allow re-picking the same file
        if (!file) return;
        if (input.id === 'siAvatarInput') siUploadAvatar(file);
        else siOpenStatusPreview(file);
    });

    // Capture phase so Enter picks the highlighted quick reply before the
    // textarea's inline Enter-to-send handler can submit the form.
    document.addEventListener('keydown', (e) => {
        if (e.target.id !== 'siReplyInput' || !siQrIsOpen()) return;
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            siQrMove(e.key === 'ArrowDown' ? 1 : -1);
        } else if (e.key === 'Escape') {
            e.preventDefault();
            siQrClose();
        } else if ((e.key === 'Enter' && !e.shiftKey) || e.key === 'Tab') {
            const active = siQrVisibleItems().find(item => item.classList.contains('active'));
            if (!active) { siQrClose(); return; } // nothing matches — let Enter send as usual
            e.preventDefault();
            e.stopImmediatePropagation();
            siQrApply(active);
        }
    }, true);

    document.addEventListener('mouseover', (e) => {
        const item = e.target.closest('[data-si-qr-item]');
        if (!item || item.classList.contains('active')) return;
        siQrItems().forEach(other => other.classList.toggle('active', other === item));
    });

    // File input change → direct POST (bypasses Livewire temp upload).
    // One listener covers every attach kind — ChatMediaController detects the
    // type from the MIME server-side, so only the picker filters differ.
    document.addEventListener('change', (e) => {
        const t = e.target;
        if (!t.matches || !t.matches('#siInputPhotos, #siInputCamera, #siInputAudio, #siInputDocs')) return;
        const files = Array.from(t.files || []);
        t.value = ''; // allow picking the same file twice in a row
        if (!files.length) return;
        // Photos and videos go through the send preview first, like my-chat;
        // audio and documents upload straight away.
        const isMedia = f => /^(image|video)\//.test(f.type || '');
        if (files.some(isMedia)) siOpenMediaPreview(files.filter(isMedia));
        // Sequential: each file becomes its own message + broadcast, and PHP's
        // per-session file lock would serialise parallel uploads anyway.
        (async () => {
            for (const f of files.filter(f => !isMedia(f))) { try { await siUploadAttachment(f); } catch (_) {} }
        })();
    }, true);

    // Send button while recording: stop and upload the voice note.
    document.addEventListener('click', (e) => {
        const sendBtn = e.target.closest('.si-input button[type="submit"]');
        if (!sendBtn) return;
        if (window.__siState.recorder) {
            e.preventDefault();
            e.stopPropagation();
            siStopRecording();
        }
    }, true);
}

function siInitVoicePlayers() {
    document.querySelectorAll('.si-msg-voice').forEach(wrap => {
        const audio = wrap.querySelector('audio');
        const waveform = wrap.querySelector('[data-si-vp-waveform]');
        if (!audio || !waveform || audio.__siVoicePlayerReady) return;
        audio.__siVoicePlayerReady = true;

        // The thread renders its own 32 bars; appending more doubled them
        // (overflowing the pill on phones) until the next morph trimmed them.
        if (!waveform.children.length) {
            let seed = Number(wrap.dataset.messageId) || 37;
            const random = () => {
                seed = (seed * 1664525 + 1013904223) >>> 0;
                return seed / 4294967296;
            };
            const bars = document.createDocumentFragment();
            for (let index = 0; index < 32; index++) {
                const bar = document.createElement('span');
                bar.className = 'si-vp-bar';
                const height = 22 + Math.round(random() * 72);
                bar.style.height = height + '%';
                bars.appendChild(bar);
            }
            waveform.appendChild(bars);
        }

        const paint = () => siPaintVoiceProgress(wrap);
        audio.addEventListener('loadedmetadata', paint);
        audio.addEventListener('durationchange', paint);
        audio.addEventListener('timeupdate', paint);
        audio.addEventListener('play', paint);
        audio.addEventListener('pause', paint);
        audio.addEventListener('ended', () => {
            audio.currentTime = 0;
            siSetVoiceButton(wrap, false);
            paint();
        });
        paint();
    });
}

function siVoiceDuration(wrap, audio) {
    const duration = Number(audio.duration);
    if (Number.isFinite(duration) && duration > 0 && duration < 86400) return duration;
    const fallback = Number(wrap.dataset.vpDuration || 0);
    return Number.isFinite(fallback) && fallback > 0 ? fallback : 0;
}

function siFormatVoiceTime(seconds) {
    const safeSeconds = Number.isFinite(seconds) && seconds >= 0 && seconds < 86400 ? seconds : 0;
    const minutes = Math.floor(safeSeconds / 60);
    const remainder = Math.floor(safeSeconds % 60);
    return minutes + ':' + String(remainder).padStart(2, '0');
}

function siPaintVoiceProgress(wrap) {
    const audio = wrap.querySelector('audio');
    const bars = wrap.querySelectorAll('.si-vp-bar');
    const timeLabel = wrap.querySelector('[data-si-vp-time]');
    if (!audio || !bars.length) return;

    const duration = siVoiceDuration(wrap, audio);
    const currentTime = Number.isFinite(audio.currentTime) && audio.currentTime >= 0 && audio.currentTime < 86400
        ? audio.currentTime
        : 0;
    const progress = duration > 0 ? Math.min(1, currentTime / duration) : 0;
    const playedBars = Math.floor(progress * bars.length);
    bars.forEach((bar, index) => bar.classList.toggle('is-played', index < playedBars));
    if (timeLabel) {
        const displayTime = audio.paused && currentTime === 0 ? duration : currentTime;
        timeLabel.textContent = siFormatVoiceTime(displayTime);
    }
}

function siSetVoiceButton(wrap, playing) {
    const button = wrap.querySelector('[data-si-vp-play]');
    if (!button) return;
    button.classList.toggle('is-playing', playing);
    button.setAttribute('aria-label', playing ? 'Pause voice message' : 'Play voice message');
    button.innerHTML = playing
        ? '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 5h4v14H6zM14 5h4v14h-4z"></path></svg>'
        : '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"></path></svg>';
}

function siToggleVoicePlayback(button) {
    const wrap = button.closest('.si-msg-voice');
    const audio = wrap?.querySelector('audio');
    if (!audio) return;

    if (audio.paused) {
        document.querySelectorAll('.si-msg-voice audio').forEach(otherAudio => {
            if (otherAudio !== audio && !otherAudio.paused) {
                otherAudio.pause();
                siSetVoiceButton(otherAudio.closest('.si-msg-voice'), false);
            }
        });
        if (!Number.isFinite(audio.currentTime) || audio.currentTime < 0 || audio.currentTime >= 86400) {
            try { audio.currentTime = 0; } catch (_) {}
        }
        audio.play().then(() => siSetVoiceButton(wrap, true)).catch(() => {});
    } else {
        audio.pause();
        siSetVoiceButton(wrap, false);
    }
}

function siSeekVoice(waveform, clientX) {
    const wrap = waveform.closest('.si-msg-voice');
    const audio = wrap?.querySelector('audio');
    if (!audio) return;
    const bounds = waveform.getBoundingClientRect();
    const duration = siVoiceDuration(wrap, audio);
    if (!bounds.width || duration <= 0) return;
    const fraction = Math.max(0, Math.min(1, (clientX - bounds.left) / bounds.width));
    audio.currentTime = fraction * duration;
    siPaintVoiceProgress(wrap);
}

function siInitBroadcastEmojiPicker() {
    const grid = document.getElementById('siBroadcastEmojiGrid');
    if (!grid || grid.dataset.ready === 'true') return;
    grid.dataset.ready = 'true';
    siRenderBroadcastEmojis();
}

function siRenderBroadcastEmojis(search = '') {
    const grid = document.getElementById('siBroadcastEmojiGrid');
    if (!grid) return;

    const query = search.trim().toLowerCase();
    const groupEntries = Object.entries(window.__siBroadcastEmojiGroups);
    const aliases = {
        face: 'smileys', faces: 'smileys', smile: 'smileys', smiles: 'smileys', laugh: 'smileys', love: 'symbols', heart: 'symbols',
        hand: 'people', hands: 'people', person: 'people', people: 'people', animal: 'animals', nature: 'animals', animals: 'animals',
        drink: 'food', drinks: 'food', food: 'food', fruit: 'food', sport: 'activities', sports: 'activities', activity: 'activities',
        travel: 'travel', transport: 'travel', vehicle: 'travel', object: 'objects', objects: 'objects', symbol: 'symbols', symbols: 'symbols', flag: 'flags', flags: 'flags'
    };
    const emojis = query
        ? groupEntries.flatMap(([category, values]) => {
            const matchesCategory = category.includes(query) || aliases[query] === category;
            return values.split(/\s+/).filter(emoji => matchesCategory || emoji.toLowerCase().includes(query));
        })
        : (window.__siBroadcastEmojiGroups[window.__siState.emojiCategory] || '').split(/\s+/);

    grid.replaceChildren();
    if (!emojis.filter(Boolean).length) {
        const empty = document.createElement('div');
        empty.className = 'si-broadcast-emoji-empty';
        empty.textContent = 'No emojis found.';
        grid.appendChild(empty);
        return;
    }

    const fragment = document.createDocumentFragment();
    [...new Set(emojis.filter(Boolean))].forEach(emoji => {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.broadcastEmoji = emoji;
        button.setAttribute('aria-label', emoji);
        button.title = emoji;
        button.textContent = emoji;
        fragment.appendChild(button);
    });
    grid.appendChild(fragment);
}

function siJumpToMessage(messageId) {
    const target = document.getElementById('si-message-' + Number(messageId));
    if (!target) return;
    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
    target.classList.remove('si-jump-highlight');
    requestAnimationFrame(() => target.classList.add('si-jump-highlight'));
    setTimeout(() => target.classList.remove('si-jump-highlight'), 1500);
}

// The thread may still be morphing in when the jump event lands, and images
// above the target grow the list as they load — retry until the message
// exists, then re-centre it as earlier media finishes loading.
function siJumpToMessageWhenReady(messageId, attempt = 0) {
    const target = document.getElementById('si-message-' + Number(messageId));
    if (!target) {
        if (attempt < 20) setTimeout(() => siJumpToMessageWhenReady(messageId, attempt + 1), 100);
        return;
    }
    siJumpToMessage(messageId);

    const pinUntil = Date.now() + 4000;
    const recentre = () => { if (Date.now() < pinUntil) target.scrollIntoView({ block: 'center' }); };
    document.querySelectorAll('#siMsgs img').forEach(img => {
        if (!img.complete) img.addEventListener('load', recentre, { once: true });
    });
    document.querySelectorAll('#siMsgs video').forEach(video => {
        if (video.readyState < 1) video.addEventListener('loadedmetadata', recentre, { once: true });
    });
}

function siInsertBroadcastEmoji(emoji) {
    const input = document.getElementById('siBroadcastText');
    if (!input || !emoji) return;

    const start = input.selectionStart ?? input.value.length;
    const end = input.selectionEnd ?? input.value.length;
    input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
    const cursor = start + emoji.length;
    input.setSelectionRange(cursor, cursor);
    input.focus();

    const root = input.closest('[wire\\:id]');
    if (root && window.Livewire) {
        Livewire.find(root.getAttribute('wire:id'))?.set('broadcastText', input.value, false);
    }
}

// options (all optional, from the send preview): conversationId — pinned when
// the preview opened, so a queue keeps going to that chat even if the admin
// opens another one mid-upload; caption; batchId — groups 2+ photos into one
// album on the customer's side; videoPreview — the thumbnail/duration the
// preview already captured.
async function siUploadAttachment(file, options = {}) {
    const root = document.querySelector('[wire\\:id]');
    const comp = root && Livewire.find(root.getAttribute('wire:id'));
    const conversationId = options.conversationId ?? comp?.$get?.('selectedConversationId') ?? comp?.selectedConversationId;
    if (!conversationId) { alert('Select a conversation first.'); return; }

    const isImage = file.type.startsWith('image/');
    const isVideo = file.type.startsWith('video/');
    const inlineMedia = isImage || isVideo;
    if (isVideo && file.size > 100 * 1024 * 1024) {
        alert('Video is larger than 100 MB. Please pick a smaller clip.');
        return;
    }

    const pending = inlineMedia ? siInsertPendingMedia(file, isVideo) : null;
    const preview = inlineMedia ? null : document.getElementById('siAttachPreview');
    const label = document.getElementById('siAttachPreviewLabel');
    const progressText = document.getElementById('siAttachProgressText');
    const progressBar = document.getElementById('siAttachProgressBar');
    const thumbWrap = document.getElementById('siAttachThumbWrap');
    const thumb = document.getElementById('siAttachThumb');
    const thumbIcon = document.getElementById('siAttachThumbIcon');

    preview?.classList.remove('is-error');
    if (preview) {
        preview.classList.add('open');
        if (label) label.textContent = 'Uploading ' + file.name;
        if (progressText) progressText.textContent = '0%';
        if (progressBar) progressBar.style.width = '0%';
        if (thumbWrap) thumbWrap.classList.remove('has-preview');
        if (thumb) thumb.removeAttribute('src');
        if (thumbIcon) thumbIcon.className = 'fa ' + (isVideo ? 'fa-file-video-o' : (isImage ? 'fa-file-image-o' : 'fa-file-o'));
    }

    const fd = new FormData();
    fd.append('conversation_id', conversationId);
    fd.append('attachment', file);
    if (options.caption) fd.append('message', options.caption);
    if (options.batchId) fd.append('batch_id', options.batchId);
    const trim = isVideo ? options.trim : null;
    if (trim) {
        fd.append('trim_start', trim.start.toFixed(3));
        fd.append('trim_end', trim.end.toFixed(3));
    }

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
        if (isVideo) {
            // A trimmed clip's poster comes from its new first frame; seeking
            // deep into a large file needs more than the usual second.
            const videoPreview = options.videoPreview || await Promise.race([
                siCaptureVideoThumbnail(file, trim ? 4000 : 1000, trim ? Math.max(0.1, trim.start) : 0.1),
                new Promise(resolve => setTimeout(() => resolve(null), trim ? 4200 : 1200)),
            ]);
            if (videoPreview?.thumbnail) fd.append('thumbnail', videoPreview.thumbnail, 'thumb.jpg');
            if (trim) fd.append('duration', String(Math.max(1, Math.round(trim.end - trim.start))));
            else if (videoPreview?.duration) fd.append('duration', String(videoPreview.duration));
            if (videoPreview?.thumbnail) {
                pending?.setThumb(videoPreview.thumbnail);
            }
        }

        if (label) label.textContent = 'Uploading ' + file.name;

        await new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/chat/attachment-upload', true);
            xhr.withCredentials = true;
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.upload.onprogress = (event) => {
                if (!event.lengthComputable) return;
                const percent = Math.max(0, Math.min(100, Math.round((event.loaded / event.total) * 100)));
                if (pending) {
                    pending.setProgress(percent);
                } else {
                    if (progressText) progressText.textContent = percent + '%';
                    if (progressBar) progressBar.style.width = percent + '%';
                    if (label) label.textContent = percent >= 100 ? 'Finishing upload…' : 'Uploading ' + file.name;
                }
            };
            xhr.onload = () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve();
                    return;
                }

                let message = 'Upload failed (' + xhr.status + ').';
                try {
                    const body = JSON.parse(xhr.responseText || '{}');
                    if (body.detail) message = body.detail;
                    else if (body.error === 'too_large') message = body.detail || 'This file is too large.';
                } catch (_) {}
                reject(new Error(message));
            };
            xhr.onerror = () => reject(new Error('Upload failed. Check your connection and try again.'));
            xhr.ontimeout = () => reject(new Error('Upload timed out. Please try again.'));
            xhr.send(fd);
        });

        if (pending) {
            pending.remove();
        } else {
            if (progressText) progressText.textContent = '100%';
            if (progressBar) progressBar.style.width = '100%';
            if (label) label.textContent = 'Upload complete';
        }
        comp?.call('refreshInbox');
    } catch (err) {
        console.error('[si-attach] upload failed', err);
        if (pending) {
            pending.markFailed(err?.message || 'Upload failed.');
        } else {
            if (preview) preview.classList.add('is-error');
            if (progressText) progressText.textContent = '!';
            if (label) label.textContent = err?.message || 'Upload failed.';
            if (progressBar) progressBar.style.width = '100%';
            setTimeout(() => preview?.classList.remove('open', 'is-error'), 4000);
        }
    } finally {
        if (!preview?.classList.contains('is-error')) {
            setTimeout(() => {
                preview?.classList.remove('open');
                thumb?.removeAttribute('src');
                thumbWrap?.classList.remove('has-preview');
            }, 500);
        }
    }
}

function siInsertPendingMedia(file, isVideo) {
    const list = document.getElementById('siMsgs');
    if (!list) return null;

    const wrap = document.createElement('div');
    wrap.className = 'si-msg sent si-pending-msg';
    const media = document.createElement('div');
    media.className = 'si-pending-media' + (isVideo ? ' video' : '');
    const urls = [];

    if (!isVideo) {
        const imageUrl = URL.createObjectURL(file);
        urls.push(imageUrl);
        media.style.backgroundImage = `url('${imageUrl}')`;
    }

    media.innerHTML = `
        <div class="si-pending-ring">
            <svg viewBox="0 0 64 64" aria-hidden="true">
                <circle class="bg" cx="32" cy="32" r="29"></circle>
                <circle class="fg" cx="32" cy="32" r="29"></circle>
            </svg>
            <span class="si-pending-percent">0%</span>
        </div>
        <div class="si-pending-label">Uploading…</div>
    `;

    const bubble = document.createElement('div');
    bubble.className = 'si-bub';
    bubble.appendChild(media);
    const time = document.createElement('div');
    time.className = 'si-btime';
    time.textContent = new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    wrap.append(bubble, time);
    list.appendChild(wrap);
    list.scrollTop = list.scrollHeight;

    const progressRing = media.querySelector('.si-pending-ring .fg');
    const progressText = media.querySelector('.si-pending-percent');
    const progressLabel = media.querySelector('.si-pending-label');
    let removed = false;

    const remove = () => {
        if (removed) return;
        removed = true;
        wrap.remove();
        urls.forEach(url => URL.revokeObjectURL(url));
    };

    return {
        setThumb(blob) {
            if (!blob) return;
            const url = URL.createObjectURL(blob);
            urls.push(url);
            media.style.backgroundImage = `url('${url}')`;
        },
        setProgress(percent) {
            const bounded = Math.max(0, Math.min(100, percent));
            if (progressRing) progressRing.style.strokeDashoffset = String(182 - (182 * bounded / 100));
            if (progressText) progressText.textContent = bounded + '%';
            if (progressLabel) progressLabel.textContent = bounded >= 100 ? 'Finishing…' : `Uploading ${bounded}%`;
        },
        markFailed(message) {
            media.classList.add('failed');
            if (progressText) progressText.textContent = '!';
            if (progressLabel) progressLabel.textContent = message || 'Upload failed';
            setTimeout(remove, 5000);
        },
        remove,
    };
}

function siCaptureVideoThumbnail(file, timeoutMs = 1000, atSeconds = 0.1) {
    return new Promise((resolve) => {
        const video = document.createElement('video');
        const objectUrl = URL.createObjectURL(file);
        let finished = false;
        let duration = 0;
        const timeout = setTimeout(() => finish(null), timeoutMs);

        const finish = (thumbnail) => {
            if (finished) return;
            finished = true;
            clearTimeout(timeout);
            video.removeAttribute('src');
            video.load();
            URL.revokeObjectURL(objectUrl);
            resolve(thumbnail ? { thumbnail, duration } : null);
        };

        const captureFrame = () => {
            try {
                const width = video.videoWidth || 640;
                const height = video.videoHeight || 360;
                const scale = width > 720 ? 720 / width : 1;
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(width * scale);
                canvas.height = Math.round(height * scale);
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(blob => finish(blob), 'image/jpeg', 0.82);
            } catch (_) {
                finish(null);
            }
        };

        video.muted = true;
        video.playsInline = true;
        video.preload = 'metadata';
        video.addEventListener('loadedmetadata', () => {
            duration = Number.isFinite(video.duration) ? Math.round(video.duration) : 0;
            try {
                video.currentTime = Math.min(atSeconds, Math.max(0, (video.duration || 0) - 0.05));
            } catch (_) {
                captureFrame();
            }
        }, { once: true });
        video.addEventListener('seeked', captureFrame, { once: true });
        video.addEventListener('error', () => finish(null), { once: true });
        video.src = objectUrl;
    });
}

// ---------- Photos & videos: send preview ----------
// my-chat's WhatsApp-style send popup (openImagePreviewModal), extended to
// videos: full preview, a caption per item, a thumbnail strip with "+" once
// there are 2+, discard-current, and send. Built outside the Livewire tree so
// inbox refreshes can't morph it away while it's open.
const SI_MPM_MAX = 20;
const SI_VIDEO_MAX_BYTES = 100 * 1024 * 1024;
// Server has ffmpeg (App\Services\VideoTrimmer): trims are cut there,
// instantly. Otherwise the browser records the trimmed clip (siTrimInBrowser).
const SI_CAN_TRIM = @json($videoTrim['ok']);
const SI_TRIM_OFF_REASON = @json($videoTrim['reason']);
const SI_TRIM_MIN_SECONDS = 1;

function siOpenMediaPreview(files) {
    const tooBig = files.filter(f => f.type.startsWith('video/') && f.size > SI_VIDEO_MAX_BYTES);
    if (tooBig.length) {
        alert(tooBig.length === 1
            ? 'Video is larger than 100 MB. Please pick a smaller clip.'
            : tooBig.length + ' videos are larger than 100 MB and were skipped.');
    }
    const accepted = files.filter(f => !tooBig.includes(f));
    if (!accepted.length) return;

    const state = window.__siState.mpm || (window.__siState.mpm = siCreateMediaPreview());
    if (!state.items.length) {
        state.conversationId = siComponent()?.$get?.('selectedConversationId') ?? null;
        if (!state.conversationId) { alert('Select a conversation first.'); return; }
    }
    const room = SI_MPM_MAX - state.items.length;
    if (room <= 0) { alert(`You can send up to ${SI_MPM_MAX} photos and videos at once.`); return; }
    if (accepted.length > room) alert(`Only ${room} more can be added (${SI_MPM_MAX} max).`);
    accepted.slice(0, room).forEach(file => state.add(file));
    state.open();
}

function siCreateMediaPreview() {
    const modal = document.createElement('div');
    modal.className = 'si-mpm';
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-label', 'Send photos and videos');
    modal.innerHTML = `
        <div class="si-mpm-topbar">
            <button type="button" class="si-mpm-icon" data-mpm-close title="Close" aria-label="Close">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <span class="si-mpm-count"></span>
            <button type="button" class="si-mpm-icon" data-mpm-discard title="Discard" aria-label="Discard">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </button>
        </div>
        <div class="si-mpm-body"></div>
        <div class="si-mpm-thumbs" aria-label="Selected photos and videos"></div>
        <div class="si-mpm-footer">
            <input type="text" class="si-mpm-caption" maxlength="1000" placeholder="Add a caption..." autocomplete="off" aria-label="Caption">
            <button type="button" class="si-mpm-send" data-mpm-send title="Send" aria-label="Send">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            </button>
        </div>
        <div class="si-mpm-busy" hidden>
            <div class="si-mpm-busy-card" role="status" aria-live="polite">
                <div class="si-mpm-busy-title"></div>
                <div class="si-mpm-busy-bar"><span></span></div>
                <div class="si-mpm-busy-note"></div>
                <button type="button" class="si-mpm-busy-btn" data-mpm-busy-btn>Cancel</button>
            </div>
        </div>`;
    document.body.appendChild(modal);

    const body = modal.querySelector('.si-mpm-body');
    const thumbs = modal.querySelector('.si-mpm-thumbs');
    const count = modal.querySelector('.si-mpm-count');
    const caption = modal.querySelector('.si-mpm-caption');
    const state = { items: [], idx: 0, conversationId: null };

    const saveCaption = () => {
        const item = state.items[state.idx];
        if (item) item.caption = caption.value;
    };
    const release = (item) => {
        URL.revokeObjectURL(item.url);
        if (item.thumbUrl) URL.revokeObjectURL(item.thumbUrl);
    };

    const renderBody = () => {
        body.querySelector('video')?.pause();
        cancelAnimationFrame(state.playFrame);
        body.innerHTML = '';
        body.classList.remove('has-trim');
        const item = state.items[state.idx];
        if (!item) return;

        const stage = document.createElement('div');
        stage.className = 'si-mpm-stage';
        const media = document.createElement(item.isVideo ? 'video' : 'img');
        media.src = item.url;
        stage.appendChild(media);
        body.appendChild(stage);
        caption.value = item.caption;
        count.textContent = `${state.idx + 1}/${state.items.length}`;
        if (!item.isVideo) { media.alt = ''; return; }

        media.controls = true;
        media.playsInline = true;
        media.preload = 'metadata';
        media.setAttribute('data-no-player', '');

        // Duration + size chip, like WhatsApp's video send screen; shows the
        // selected length once the clip is trimmed.
        const meta = document.createElement('div');
        meta.className = 'si-mpm-meta';
        meta.innerHTML = '<i class="fa fa-video"></i> <span></span>';
        body.appendChild(meta);
        const size = (item.file.size / 1024 / 1024).toFixed(1) + ' MB';
        const updateMeta = () => {
            const text = meta.querySelector('span');
            if (!item.duration) { text.textContent = size; return; }
            text.textContent = siIsTrimmed(item)
                ? siFormatClipTime(item.trim.end - item.trim.start) + ' of ' + siFormatClipTime(item.duration)
                : siFormatClipTime(item.duration) + ' · ' + size;
        };
        updateMeta();

        const setup = () => {
            if (!Number.isFinite(media.duration) || media.duration <= 0) return;
            item.duration = media.duration;
            if (!item.trim) item.trim = { start: 0, end: media.duration };
            updateMeta();
            const canTrim = SI_CAN_TRIM || siCanTrimInBrowser();
            if (canTrim && item.duration > SI_TRIM_MIN_SECONDS + 0.5) {
                body.classList.add('has-trim');
                body.appendChild(siBuildTrimBar(item, media, updateMeta));
            } else if (!canTrim) {
                const note = document.createElement('div');
                note.className = 'si-mpm-trim-off';
                note.textContent = "Trimming isn't available in this browser — "
                    + (SI_TRIM_OFF_REASON || 'ffmpeg is not available').replace(/\.+\s*$/, '') + '. The video will be sent whole.';
                body.appendChild(note);
            }
        };
        media.addEventListener('loadedmetadata', setup, { once: true });

        // Keep playback inside the trimmed range: start from the in-point and
        // stop at the out-point (checked per frame — timeupdate is too coarse).
        const watch = () => {
            if (item.trim && media.currentTime >= item.trim.end - 0.02) {
                media.pause();
                media.currentTime = item.trim.start;
            }
            body.querySelector('.si-mpm-trim')?.__siSyncPlayhead?.();
            if (!media.paused) state.playFrame = requestAnimationFrame(watch);
        };
        media.addEventListener('play', () => {
            if (item.trim && (media.currentTime < item.trim.start || media.currentTime >= item.trim.end - 0.05)) {
                media.currentTime = item.trim.start;
            }
            cancelAnimationFrame(state.playFrame);
            state.playFrame = requestAnimationFrame(watch);
        });
        media.addEventListener('seeked', () => {
            // The native scrubber can still reach outside the range.
            if (!item.trim) return;
            const clamped = Math.min(Math.max(media.currentTime, item.trim.start), item.trim.end);
            if (Math.abs(clamped - media.currentTime) > 0.05) media.currentTime = clamped;
            body.querySelector('.si-mpm-trim')?.__siSyncPlayhead?.();
        });
    };

    const renderThumbs = () => {
        thumbs.classList.toggle('open', state.items.length > 1);
        thumbs.innerHTML = '';
        state.items.forEach((item, index) => {
            const tile = document.createElement('button');
            tile.type = 'button';
            tile.className = 'si-mpm-thumb' + (index === state.idx ? ' active' : '') + (item.isVideo ? ' is-video' : '');
            const background = item.isVideo ? item.thumbUrl : item.url;
            if (background) tile.style.backgroundImage = `url('${background}')`;
            tile.dataset.mpmIndex = index;
            tile.setAttribute('aria-label', (item.isVideo ? 'Video ' : 'Photo ') + (index + 1));
            thumbs.appendChild(tile);
        });
        if (state.items.length < SI_MPM_MAX) {
            const add = document.createElement('button');
            add.type = 'button';
            add.className = 'si-mpm-thumb si-mpm-thumb-add';
            add.dataset.mpmAdd = '';
            add.textContent = '+';
            add.setAttribute('aria-label', 'Add more photos or videos');
            thumbs.appendChild(add);
        }
    };

    state.render = () => { renderBody(); renderThumbs(); };

    state.add = (file) => {
        const item = { file, isVideo: file.type.startsWith('video/'), url: URL.createObjectURL(file), caption: '', preview: null, thumbUrl: null };
        state.items.push(item);
        if (!item.isVideo) return;
        // Frame for the strip; reused at send time as the poster + duration.
        siCaptureVideoThumbnail(file, 8000).then(preview => {
            if (!preview || !state.items.includes(item)) return;
            item.preview = preview;
            item.thumbUrl = URL.createObjectURL(preview.thumbnail);
            renderThumbs();
        });
    };

    state.open = () => {
        modal.classList.add('open');
        // Hides the floating "Support calls" pill, which sits over the caption.
        document.body.classList.add('si-mpm-open');
        state.render();
        // Skip on phones: the keyboard would cover the preview straight away.
        if (!window.matchMedia('(hover: none)').matches) setTimeout(() => caption.focus(), 100);
    };

    state.close = () => {
        body.querySelector('video')?.pause();
        cancelAnimationFrame(state.playFrame);
        busy.hidden = true;
        modal.classList.remove('open');
        document.body.classList.remove('si-mpm-open');
        state.items.forEach(release);
        state.items = [];
        state.idx = 0;
        body.innerHTML = '';
    };

    // Discard removes only the item on screen; the last one closes the popup.
    state.discard = () => {
        const [item] = state.items.splice(state.idx, 1);
        if (item) release(item);
        if (!state.items.length) { state.close(); return; }
        state.idx = Math.min(state.idx, state.items.length - 1);
        state.render();
    };

    // Progress overlay for in-browser trimming. While busy the overlay
    // covers the popup, and its button cancels.
    const busy = modal.querySelector('.si-mpm-busy');
    const showBusy = (title, fraction, note) => {
        busy.hidden = false;
        busy.classList.remove('is-error');
        busy.querySelector('.si-mpm-busy-title').textContent = title;
        busy.querySelector('.si-mpm-busy-bar span').style.width = Math.round(Math.min(1, Math.max(0, fraction)) * 100) + '%';
        busy.querySelector('.si-mpm-busy-note').textContent = note;
        busy.querySelector('[data-mpm-busy-btn]').textContent = 'Cancel';
    };
    const showBusyError = (message) => {
        busy.hidden = false;
        busy.classList.add('is-error');
        busy.querySelector('.si-mpm-busy-title').textContent = "Couldn't trim the video";
        busy.querySelector('.si-mpm-busy-note').textContent = message;
        busy.querySelector('[data-mpm-busy-btn]').textContent = 'OK';
    };

    // No ffmpeg on the server: record each trimmed video here first, the
    // way the new-profile page does. Finished clips are kept on the item,
    // so after a failure or cancel, Send picks up where it stopped.
    state.trimInBrowser = async (items) => {
        state.busy = true;
        body.querySelector('video')?.pause();
        const controller = new AbortController();
        state.abortTrim = () => controller.abort();
        // Created inside the Send click so the browser lets it run; it keeps
        // the clip's sound in the recording without playing it aloud.
        let audioContext = null;
        try { audioContext = new (window.AudioContext || window.webkitAudioContext)(); } catch (_) {}
        try {
            for (const [index, item] of items.entries()) {
                const title = items.length > 1 ? `Trimming video ${index + 1} of ${items.length}…` : 'Trimming video…';
                const { start, end } = item.trim;
                const note = `Recording the ${siFormatClipTime(end - start)} you selected — this takes about as long as the clip. Keep this tab open.`;
                showBusy(title, 0, note);
                const file = await siTrimInBrowser(item.file, start, end, {
                    audioContext,
                    signal: controller.signal,
                    onProgress: fraction => showBusy(title, fraction, note),
                });
                // Poster from the original at the new start (a recording's
                // first frames can be blank while the encoder warms up).
                const poster = await siCaptureVideoThumbnail(item.file, 4000, Math.max(0.1, start));
                item.trimmed = {
                    key: siTrimKey(item),
                    file,
                    preview: { thumbnail: poster?.thumbnail || null, duration: Math.max(1, Math.round(end - start)) },
                };
            }
            busy.hidden = true;
            return true;
        } catch (err) {
            if (err?.name === 'AbortError') busy.hidden = true;
            else showBusyError(err?.message || 'Trimming failed.');
            return false;
        } finally {
            state.busy = false;
            state.abortTrim = null;
            audioContext?.close?.().catch(() => {});
        }
    };

    state.send = async () => {
        if (state.busy) return;
        saveCaption();

        if (!SI_CAN_TRIM) {
            const pending = state.items.filter(item => item.isVideo && siIsTrimmed(item) && item.trimmed?.key !== siTrimKey(item));
            if (pending.length && !await state.trimInBrowser(pending)) return;
        }

        // 2+ photos share one batch_id so the customer sees one album grid.
        const photoCount = state.items.filter(item => !item.isVideo).length;
        const batchId = photoCount > 1 ? siUuid() : null;
        const conversationId = state.conversationId;
        const queue = state.items.map(item => {
            const trimmed = item.isVideo && siIsTrimmed(item);
            const options = {
                conversationId,
                caption: item.caption.trim(),
                batchId: item.isVideo ? null : batchId,
                videoPreview: trimmed ? null : item.preview,
                trim: null,
            };
            if (trimmed && SI_CAN_TRIM) {
                // The server cuts the range; the poster is re-taken at the
                // new start rather than reusing the frame-0 capture.
                options.trim = { ...item.trim };
            } else if (trimmed) {
                // Already cut in the browser above: upload that clip.
                return { file: item.trimmed.file, options: { ...options, videoPreview: item.trimmed.preview } };
            }
            return { file: item.file, options };
        });
        state.close();
        // One at a time: parallel uploads collide on PHP's session lock.
        (async () => {
            for (const job of queue) {
                try { await siUploadAttachment(job.file, job.options); } catch (_) {}
            }
        })();
    };

    modal.addEventListener('click', (e) => {
        if (e.target.closest('[data-mpm-busy-btn]')) {
            if (state.busy) state.abortTrim?.();
            else busy.hidden = true;
            return;
        }
        if (state.busy) return;
        if (e.target.closest('[data-mpm-close]')) { state.close(); return; }
        if (e.target.closest('[data-mpm-discard]')) { state.discard(); return; }
        if (e.target.closest('[data-mpm-send]')) { state.send(); return; }
        if (e.target.closest('[data-mpm-add]')) {
            saveCaption();
            document.getElementById('siInputPhotos')?.click();
            return;
        }
        const tile = e.target.closest('[data-mpm-index]');
        if (tile) {
            saveCaption();
            state.idx = Number(tile.dataset.mpmIndex);
            state.render();
        }
    });
    caption.addEventListener('input', saveCaption);
    caption.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.isComposing) { e.preventDefault(); state.send(); }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape' || !modal.classList.contains('open')) return;
        if (state.busy) state.abortTrim?.();
        else if (!busy.hidden) busy.hidden = true;
        else state.close();
    });

    return state;
}

function siTrimKey(item) {
    return item.trim.start.toFixed(2) + '-' + item.trim.end.toFixed(2);
}

// The browser can record a clip itself (the new-profile page's technique).
function siCanTrimInBrowser() {
    return typeof window.MediaRecorder === 'function'
        && (typeof HTMLMediaElement.prototype.captureStream === 'function'
            || typeof HTMLMediaElement.prototype.mozCaptureStream === 'function'
            || typeof HTMLCanvasElement.prototype.captureStream === 'function');
}

function siMediaEvent(element, event, ms = 8000) {
    return new Promise((resolve, reject) => {
        const timer = setTimeout(() => reject(new Error('The video took too long to load.')), ms);
        element.addEventListener(event, () => { clearTimeout(timer); resolve(); }, { once: true });
        element.addEventListener('error', () => { clearTimeout(timer); reject(new Error("The video couldn't be read.")); }, { once: true });
    });
}

// In-browser trim, as on the new-profile page: play [start, end] in a hidden
// <video> and record it with MediaRecorder. Runs in real time, so it's only
// used when the server has no ffmpeg. Sound goes through Web Audio into the
// recording alone, so nothing plays aloud; MP4 is preferred so iPhones can
// play the result. Resolves to a File; rejects with AbortError on cancel.
async function siTrimInBrowser(file, start, end, { audioContext, signal, onProgress } = {}) {
    const url = URL.createObjectURL(file);
    const video = document.createElement('video');
    video.src = url;
    video.playsInline = true;
    video.preload = 'auto';
    video.setAttribute('data-no-player', '');
    video.style.cssText = 'position:fixed;left:-9999px;top:0;width:2px;height:2px;';
    document.body.appendChild(video);
    let drawTimer = null;
    let recorder = null;

    try {
        await siMediaEvent(video, 'loadedmetadata');
        video.currentTime = start;
        await siMediaEvent(video, 'seeked');

        let audioTrack = null;
        if (audioContext) {
            const destination = audioContext.createMediaStreamDestination();
            audioContext.createMediaElementSource(video).connect(destination);
            audioTrack = destination.stream.getAudioTracks()[0] || null;
        }

        // Video straight from the element; through a canvas where the
        // browser can't capture a <video> (Safari).
        const capture = video.captureStream || video.mozCaptureStream;
        let videoStream = capture ? capture.call(video) : null;
        if (!videoStream) {
            const canvas = document.createElement('canvas');
            const scale = Math.min(1, 1280 / (video.videoWidth || 1280));
            canvas.width = Math.round((video.videoWidth || 1280) * scale);
            canvas.height = Math.round((video.videoHeight || 720) * scale);
            const context = canvas.getContext('2d');
            drawTimer = setInterval(() => context.drawImage(video, 0, 0, canvas.width, canvas.height), 1000 / 30);
            videoStream = canvas.captureStream(30);
        }

        // captureStream() adds its track once a frame is decoded; after the
        // seek above that's normally immediate.
        for (let i = 0; i < 20 && !videoStream.getVideoTracks().length; i++) {
            await new Promise(resolve => setTimeout(resolve, 50));
        }
        const videoTrack = videoStream.getVideoTracks()[0];
        if (!videoTrack) throw new Error("This browser can't record the video for trimming.");

        const mimeType = [
            'video/mp4;codecs=avc1.42E01E,mp4a.40.2', 'video/mp4;codecs=avc1,mp4a', 'video/mp4',
            'video/webm;codecs=vp9,opus', 'video/webm;codecs=vp8,opus', 'video/webm',
        ].find(type => MediaRecorder.isTypeSupported(type));
        if (!mimeType) throw new Error("This browser can't record video for trimming.");

        // Roughly keep the source quality: its average bitrate, 1–4 Mbps.
        const sourceBitrate = video.duration ? (file.size * 8) / video.duration : 2500000;
        recorder = new MediaRecorder(new MediaStream([videoTrack, audioTrack].filter(Boolean)), {
            mimeType,
            videoBitsPerSecond: Math.round(Math.min(4000000, Math.max(1000000, sourceBitrate))),
        });
        const chunks = [];
        recorder.ondataavailable = event => { if (event.data?.size) chunks.push(event.data); };
        const stopped = new Promise(resolve => { recorder.onstop = resolve; });
        // Record from the moment playback is running; starting the recorder
        // first put up to a second of frozen first frame at the start.
        try {
            await video.play();
        } catch (_) {
            throw new Error('The browser blocked playback needed for trimming. Press Send again.');
        }
        recorder.start(250);

        // Stop at the out-point. The 50 ms timer is the precise check; the
        // media's own timeupdate is a backstop for when the browser delays
        // timers. A hidden tab may also stop decoding the video, so leaving
        // the tab aborts rather than producing a frozen clip.
        await new Promise((resolve, reject) => {
            let timer = null;
            const finish = (error) => {
                clearInterval(timer);
                video.removeEventListener('timeupdate', check);
                error ? reject(error) : resolve();
            };
            const check = () => {
                if (signal?.aborted) return finish(new DOMException('Cancelled', 'AbortError'));
                if (document.hidden) return finish(new Error('Trimming needs this tab to stay open. Press Send to try again.'));
                onProgress?.((video.currentTime - start) / Math.max(0.001, end - start));
                if (video.currentTime >= end - 0.03 || video.ended) finish();
            };
            timer = setInterval(check, 50);
            video.addEventListener('timeupdate', check);
        });

        recorder.stop();
        video.pause();
        await stopped;
        onProgress?.(1);

        const type = mimeType.split(';')[0];
        const base = (file.name || 'video').replace(/\.[^.]+$/, '') || 'video';
        return new File(chunks, `${base}-trimmed.${type === 'video/mp4' ? 'mp4' : 'webm'}`, { type });
    } finally {
        clearInterval(drawTimer);
        if (recorder && recorder.state !== 'inactive') { try { recorder.stop(); } catch (_) {} }
        video.pause();
        video.removeAttribute('src');
        video.load();
        video.remove();
        URL.revokeObjectURL(url);
    }
}

function siIsTrimmed(item) {
    return !!(item.trim && item.duration)
        && (item.trim.start > 0.05 || item.trim.end < item.duration - 0.05);
}

// 0:07, 1:05 — tenths only for short clips, where they matter.
function siFormatClipTime(seconds, tenths = false) {
    const safe = Math.max(0, seconds || 0);
    const minutes = Math.floor(safe / 60);
    const rest = safe - minutes * 60;
    const secs = tenths ? rest.toFixed(1).padStart(4, '0') : String(Math.floor(rest)).padStart(2, '0');
    return minutes + ':' + secs;
}

// WhatsApp-style trim bar: a filmstrip of the clip with a start and an end
// handle. Dragging a handle previews that frame; the range lives on
// item.trim and goes to the server with the upload.
function siBuildTrimBar(item, media, onChange) {
    const bar = document.createElement('div');
    bar.className = 'si-mpm-trim';
    bar.innerHTML = `
        <div class="si-mpm-strip">
            <div class="si-mpm-frames"></div>
            <div class="si-mpm-shade start"></div>
            <div class="si-mpm-shade end"></div>
            <div class="si-mpm-window"></div>
            <div class="si-mpm-playhead"></div>
            <button type="button" class="si-mpm-handle start" data-handle="start" role="slider" aria-label="Trim start"></button>
            <button type="button" class="si-mpm-handle end" data-handle="end" role="slider" aria-label="Trim end"></button>
        </div>
        <div class="si-mpm-trim-times">
            <span data-time="start"></span>
            <strong data-time="length"></strong>
            <span data-time="end"></span>
        </div>`;

    const strip = bar.querySelector('.si-mpm-strip');
    const frames = bar.querySelector('.si-mpm-frames');
    const pct = t => (t / item.duration) * 100 + '%';

    const paint = () => {
        const { start, end } = item.trim;
        bar.querySelector('.si-mpm-shade.start').style.width = pct(start);
        bar.querySelector('.si-mpm-shade.end').style.left = pct(end);
        const win = bar.querySelector('.si-mpm-window');
        win.style.left = pct(start);
        win.style.right = (100 - (end / item.duration) * 100) + '%';
        bar.querySelector('.si-mpm-handle.start').style.left = pct(start);
        bar.querySelector('.si-mpm-handle.end').style.left = pct(end);
        bar.querySelector('[data-time="start"]').textContent = siFormatClipTime(start, true);
        bar.querySelector('[data-time="end"]').textContent = siFormatClipTime(end, true);
        bar.querySelector('[data-time="length"]').textContent = siFormatClipTime(end - start);
        ['start', 'end'].forEach(side => {
            const handle = bar.querySelector('.si-mpm-handle.' + side);
            handle.setAttribute('aria-valuemin', '0');
            handle.setAttribute('aria-valuemax', String(Math.round(item.duration)));
            handle.setAttribute('aria-valuenow', String(Math.round(item.trim[side])));
            handle.setAttribute('aria-valuetext', siFormatClipTime(item.trim[side], true));
        });
        onChange();
    };
    bar.__siSyncPlayhead = () => {
        bar.querySelector('.si-mpm-playhead').style.left = pct(media.currentTime);
    };

    // Move one handle, keeping at least SI_TRIM_MIN_SECONDS selected, and
    // show the frame at the new edge.
    const setEdge = (side, time) => {
        const { start, end } = item.trim;
        item.trim[side] = side === 'start'
            ? Math.min(Math.max(0, time), end - SI_TRIM_MIN_SECONDS)
            : Math.max(Math.min(item.duration, time), start + SI_TRIM_MIN_SECONDS);
        media.pause();
        media.currentTime = item.trim[side];
        paint();
        bar.__siSyncPlayhead();
    };
    const timeAt = clientX => {
        const rect = strip.getBoundingClientRect();
        return Math.min(Math.max(0, (clientX - rect.left) / rect.width), 1) * item.duration;
    };

    strip.addEventListener('pointerdown', (e) => {
        const handle = e.target.closest('[data-handle]');
        e.preventDefault();
        if (!handle) {
            // Tap the strip to scrub inside the selection.
            media.pause();
            media.currentTime = Math.min(Math.max(timeAt(e.clientX), item.trim.start), item.trim.end);
            bar.__siSyncPlayhead();
            return;
        }
        handle.focus();
        handle.setPointerCapture(e.pointerId);
        handle.classList.add('dragging');
        const side = handle.dataset.handle;
        const move = ev => setEdge(side, timeAt(ev.clientX));
        const up = () => {
            handle.classList.remove('dragging');
            handle.removeEventListener('pointermove', move);
            handle.removeEventListener('pointerup', up);
            handle.removeEventListener('pointercancel', up);
        };
        handle.addEventListener('pointermove', move);
        handle.addEventListener('pointerup', up);
        handle.addEventListener('pointercancel', up);
    });
    strip.addEventListener('keydown', (e) => {
        const handle = e.target.closest('[data-handle]');
        if (!handle || !['ArrowLeft', 'ArrowRight'].includes(e.key)) return;
        e.preventDefault();
        const step = (e.shiftKey ? 0.1 : 1) * (e.key === 'ArrowLeft' ? -1 : 1);
        setEdge(handle.dataset.handle, item.trim[handle.dataset.handle] + step);
    });

    // Filmstrip frames are generated once per clip and reused when the admin
    // flips between items.
    item.framesPromise = item.framesPromise || siVideoFrames(item.file, 10);
    item.framesPromise.then(urls => {
        frames.innerHTML = '';
        urls.forEach(url => {
            const img = document.createElement('img');
            img.src = url;
            img.alt = '';
            frames.appendChild(img);
        });
    });

    paint();
    bar.__siSyncPlayhead();
    return bar;
}

// Evenly spaced JPEG frames for the trim filmstrip. Best effort: whatever
// frames decode in time are returned, an empty list leaves a plain strip.
async function siVideoFrames(file, count) {
    const video = document.createElement('video');
    const url = URL.createObjectURL(file);
    const wait = (event, ms) => new Promise(resolve => {
        const timer = setTimeout(() => resolve(false), ms);
        video.addEventListener(event, () => { clearTimeout(timer); resolve(true); }, { once: true });
    });
    video.muted = true;
    video.playsInline = true;
    video.preload = 'auto';
    video.src = url;

    const frames = [];
    try {
        if (!await wait('loadedmetadata', 8000) || !Number.isFinite(video.duration)) return frames;
        const height = 96;
        const width = Math.round(height * ((video.videoWidth || 16) / (video.videoHeight || 9)));
        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const context = canvas.getContext('2d');
        for (let index = 0; index < count; index++) {
            video.currentTime = video.duration * (index + 0.5) / count;
            if (!await wait('seeked', 4000)) break;
            context.drawImage(video, 0, 0, width, height);
            frames.push(canvas.toDataURL('image/jpeg', 0.6));
        }
    } catch (_) {
        // Keep whatever frames we already have.
    } finally {
        video.removeAttribute('src');
        video.load();
        URL.revokeObjectURL(url);
    }
    return frames;
}

function siUuid() {
    if (window.crypto?.randomUUID) return window.crypto.randomUUID();
    return 'b-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
}

// Dispatcher for the paperclip menu. File kinds open their own hidden input
// and ride the shared upload path; Location and Contact compose a text
// message instead, exactly like the customer app.
function siHandleAttachKind(kind) {
    switch (kind) {
        case 'photos':   document.getElementById('siInputPhotos')?.click(); break;
        case 'camera':   document.getElementById('siInputCamera')?.click(); break;
        case 'audio':    document.getElementById('siInputAudio')?.click();  break;
        case 'docs':     document.getElementById('siInputDocs')?.click();   break;
        case 'location': siShareLocation(); break;
        case 'contact':  siOpenContact();   break;
        case 'quick':    siQrOpenFromMenu(); break;
    }
}

// Chevron menu on a chat / call row. Stops the click here so it never bubbles
// up to the row's own wire:click (open the chat). A menu item's wire:click
// has already fired by the time the click reaches this.
function siRowMenu(event, wrap) {
    event.stopPropagation();
    const opening = !!event.target.closest('.si-row-actions-trigger') && !wrap.classList.contains('open');
    document.querySelectorAll('.si-row-actions.open').forEach(menu => menu.classList.remove('open'));
    if (!opening) return;
    const list = wrap.closest('.si-list');
    const roomBelow = list ? list.getBoundingClientRect().bottom - wrap.getBoundingClientRect().bottom : Infinity;
    // Open first so the menu has a height to measure (chat rows have two items, call rows one).
    wrap.classList.remove('up');
    wrap.classList.add('open');
    const menu = wrap.querySelector('.si-row-actions-menu');
    wrap.classList.toggle('up', roomBelow < (menu ? menu.offsetHeight + 8 : 90));
}

function siComponent() {
    const root = document.querySelector('.s-modern')?.closest('[wire\\:id]');
    return root ? Livewire.find(root.getAttribute('wire:id')) : null;
}

function siUploadError(xhrOrBody, fallback) {
    const body = xhrOrBody || {};
    const firstError = body.errors ? Object.values(body.errors)[0]?.[0] : null;
    return firstError || body.detail || body.message || fallback;
}

// ---------- Status: photo / video ----------
// Preview + optional caption before posting, like WhatsApp. Built outside the
// Livewire tree so inbox refreshes can't morph it away mid-upload.
function siOpenStatusPreview(file) {
    const isVideo = file.type.startsWith('video/');
    if (!isVideo && !file.type.startsWith('image/')) { alert('Choose a photo or video.'); return; }
    if (file.size > 30 * 1024 * 1024) { alert('Status files must be 30 MB or smaller.'); return; }

    const url = URL.createObjectURL(file);
    const overlay = document.createElement('div');
    overlay.className = 'si-stp-overlay';
    overlay.innerHTML = `
        <div class="si-stp-card" role="dialog" aria-modal="true" aria-label="New status">
            <h5>New ${isVideo ? 'video' : 'photo'} status</h5>
            <div class="si-stp-media"></div>
            <input type="text" maxlength="500" placeholder="Add a caption (optional)" aria-label="Caption">
            <div class="si-stp-progress" hidden><span></span></div>
            <div class="si-stp-error" hidden></div>
            <div class="si-stp-actions">
                <button type="button" class="ghost" data-stp-cancel>Cancel</button>
                <button type="button" class="primary" data-stp-post>Post status</button>
            </div>
        </div>`;
    const media = document.createElement(isVideo ? 'video' : 'img');
    media.src = url;
    if (isVideo) { media.controls = true; media.muted = true; media.playsInline = true; }
    overlay.querySelector('.si-stp-media').appendChild(media);
    document.body.appendChild(overlay);

    const caption = overlay.querySelector('input');
    const progress = overlay.querySelector('.si-stp-progress');
    const bar = progress.querySelector('span');
    const error = overlay.querySelector('.si-stp-error');
    const postButton = overlay.querySelector('[data-stp-post]');
    const cancelButton = overlay.querySelector('[data-stp-cancel]');
    let busy = false;

    const close = () => {
        if (busy) return;
        document.removeEventListener('keydown', onKey);
        overlay.remove();
        URL.revokeObjectURL(url);
    };
    const fail = (message) => {
        busy = false;
        postButton.disabled = cancelButton.disabled = false;
        progress.hidden = true;
        error.textContent = message;
        error.hidden = false;
    };
    const post = () => {
        if (busy) return;
        busy = true;
        postButton.disabled = cancelButton.disabled = true;
        error.hidden = true;
        progress.hidden = false;
        bar.style.width = '0%';

        const form = new FormData();
        form.append('media', file);
        if (caption.value.trim()) form.append('content', caption.value.trim());

        const xhr = new XMLHttpRequest();
        xhr.open('POST', @json(route('status.upload')), true);
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.upload.onprogress = (event) => {
            if (event.lengthComputable) bar.style.width = Math.round((event.loaded / event.total) * 100) + '%';
        };
        xhr.onload = () => {
            let body = {};
            try { body = JSON.parse(xhr.responseText || '{}'); } catch (_) {}
            if (xhr.status >= 200 && xhr.status < 300 && body.status_id) {
                busy = false;
                close();
                siComponent()?.call('statusUploaded', body.status_id);
            } else {
                fail(siUploadError(body, 'Upload failed (' + xhr.status + ').'));
            }
        };
        xhr.onerror = () => fail('Upload failed. Check your connection and try again.');
        xhr.send(form);
    };
    const onKey = (event) => {
        if (event.key === 'Escape') close();
        if (event.key === 'Enter' && event.target === caption) { event.preventDefault(); post(); }
    };

    document.addEventListener('keydown', onKey);
    overlay.addEventListener('click', (event) => {
        if (event.target === overlay || event.target.closest('[data-stp-cancel]')) close();
        else if (event.target.closest('[data-stp-post]')) post();
    });
    caption.focus();
}

// ---------- Settings → Account: profile photo ----------
// Same /settings/avatar endpoint the customer app's Account pane uses.
async function siUploadAvatar(file) {
    if (!file.type.startsWith('image/')) { alert('Choose an image file.'); return; }
    if (file.size > 5 * 1024 * 1024) { alert('Profile photos must be 5 MB or smaller.'); return; }

    const avatar = document.getElementById('siAcctAvatar');
    const caption = document.getElementById('siAcctAvatarCaption');
    avatar?.classList.add('is-uploading');
    if (caption) { caption.textContent = 'Uploading…'; caption.classList.remove('error'); }

    const form = new FormData();
    form.append('avatar', file);
    try {
        const response = await fetch(@json(route('settings.avatar')), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: form,
        });
        if (!response.ok) {
            let body = {};
            try { body = await response.json(); } catch (_) {}
            throw new Error(siUploadError(body, 'Upload failed (' + response.status + ').'));
        }
        siComponent()?.$refresh();
    } catch (err) {
        avatar?.classList.remove('is-uploading');
        if (caption) { caption.textContent = err.message || 'Upload failed.'; caption.classList.add('error'); }
    }
}

// ---------- Quick replies ----------
// "/" as the first character of an otherwise single-word composer opens the
// picker in 'slash' mode (the pick replaces "/query"); the paperclip entry
// opens it in 'browse' mode over an existing draft (the pick is inserted at
// the caret). {name} becomes the customer's first name.
function siQrMenuEl() { return document.getElementById('siQrMenu'); }
function siQrItems() { return Array.from(siQrMenuEl()?.querySelectorAll('[data-si-qr-item]') || []); }
function siQrVisibleItems() { return siQrItems().filter(item => !item.hidden); }
function siQrIsOpen() { return !!siQrMenuEl()?.classList.contains('open'); }

function siQrOpen(mode, query = '') {
    const menu = siQrMenuEl();
    if (!menu) return;
    window.__siState.qrMode = mode;
    siQrFilter(query);
    menu.classList.add('open');
}

function siQrClose() {
    siQrMenuEl()?.classList.remove('open');
    window.__siState.qrMode = null;
}

function siQrFilter(query) {
    const needle = query.toLowerCase();
    let first = true;
    siQrItems().forEach(item => {
        const matches = !needle
            || item.dataset.shortcut.toLowerCase().includes(needle)
            || item.dataset.message.toLowerCase().includes(needle);
        item.hidden = !matches;
        item.classList.toggle('active', matches && first);
        if (matches) first = false;
    });
    const empty = siQrMenuEl()?.querySelector('[data-si-qr-empty]');
    if (empty) empty.hidden = !first;
}

function siQrMove(delta) {
    const items = siQrVisibleItems();
    if (!items.length) return;
    const current = items.findIndex(item => item.classList.contains('active'));
    const next = (current + delta + items.length) % items.length;
    items.forEach((item, index) => item.classList.toggle('active', index === next));
    items[next].scrollIntoView({ block: 'nearest' });
}

function siQrSync(textarea) {
    const slash = /^\/(\S*)$/.exec(textarea.value);
    if (slash) siQrOpen('slash', slash[1]);
    else if (siQrIsOpen()) siQrClose();
}

function siQrOpenFromMenu() {
    const textarea = document.getElementById('siReplyInput');
    if (!textarea) return;
    if (textarea.value.trim() === '') {
        textarea.value = '/';
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    } else {
        siQrOpen('browse');
    }
    textarea.focus();
}

function siQrApply(item) {
    const textarea = document.getElementById('siReplyInput');
    if (!textarea || !item) return;

    const name = textarea.closest('form')?.dataset.siCustomerName || '';
    const text = item.dataset.message.replace(/\{name\}/gi, name);
    let caret;
    if (window.__siState.qrMode === 'browse') {
        const start = textarea.selectionStart ?? textarea.value.length;
        const end = textarea.selectionEnd ?? textarea.value.length;
        const before = textarea.value.slice(0, start);
        const separator = before && !/\s$/.test(before) ? ' ' : '';
        textarea.value = before + separator + text + textarea.value.slice(end);
        caret = (before + separator + text).length;
    } else {
        textarea.value = text;
        caret = text.length;
    }

    siQrClose();
    // Let wire:model pick the value up; siQrSync re-runs but won't reopen.
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    textarea.focus();
    textarea.setSelectionRange(caret, caret);
}

// Stuff a text body into the composer and fire the normal sendReply path so
// the message persists + broadcasts like anything else typed here.
function siSendText(body) {
    const ta = document.getElementById('siReplyInput');
    if (!ta) { alert('Select a conversation first.'); return; }
    ta.value = body;
    const root = ta.closest('[wire\\:id]');
    if (root && window.Livewire) {
        const comp = Livewire.find(root.getAttribute('wire:id'));
        // Set without a live round-trip; the submit below carries the value.
        try { comp?.set('reply', body, false); } catch (_) {}
    }
    ta.closest('form')?.requestSubmit();
}

window.siShareLocation = function () {
    if (!navigator.geolocation) { alert('Geolocation is not supported by this browser.'); return; }
    const btn = document.getElementById('siAttachBtn');
    btn && btn.setAttribute('disabled', 'disabled');
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            btn && btn.removeAttribute('disabled');
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            siSendText(`📍 Support location: https://maps.google.com/?q=${lat},${lng}`);
        },
        (err) => {
            btn && btn.removeAttribute('disabled');
            console.warn('[si-geo] failed', err);
            alert('Could not get your location. Make sure location access is allowed.');
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
    );
};

window.siOpenContact = function () {
    const overlay = document.getElementById('siContactOverlay');
    if (!overlay) return;
    document.getElementById('siContactName').value = '';
    document.getElementById('siContactPhone').value = '';
    overlay.classList.add('open');
    setTimeout(() => document.getElementById('siContactName')?.focus(), 60);
};
window.siCloseContact = function () {
    document.getElementById('siContactOverlay')?.classList.remove('open');
};
window.siSubmitContact = function () {
    const name  = (document.getElementById('siContactName').value || '').trim();
    const phone = (document.getElementById('siContactPhone').value || '').trim();
    if (!name && !phone) { alert('Enter at least a name or a phone number.'); return; }
    siCloseContact();
    siSendText(`👤 Contact\nName: ${name || '—'}\nPhone: ${phone || '—'}`);
};

// See fixAudioDuration in chat.blade.php — same trick for the admin inbox.
window.siFixAudioDuration = function (audio) {
    if (!audio || audio.__durationFixed) return;
    audio.__durationFixed = true;
    if (audio.duration === Infinity || isNaN(audio.duration)) {
        const onChange = () => {
            if (audio.duration !== Infinity && !isNaN(audio.duration)) {
                audio.removeEventListener('durationchange', onChange);
                audio.currentTime = 0;
            }
        };
        audio.addEventListener('durationchange', onChange);
        try { audio.currentTime = 1e101; } catch (_) {}
    }
};

async function siStartRecording() {
    if (window.__siState.recorder) return;
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        let mime = 'audio/webm';
        if (!MediaRecorder.isTypeSupported(mime)) {
            mime = MediaRecorder.isTypeSupported('audio/mp4') ? 'audio/mp4' : '';
        }
        const rec = new MediaRecorder(stream, mime ? { mimeType: mime } : {});
        const chunks = [];
        rec.ondataavailable = (ev) => { if (ev.data && ev.data.size > 0) chunks.push(ev.data); };
        rec.onstop = () => {
            stream.getTracks().forEach(t => t.stop());
            const blob = new Blob(chunks, { type: rec.mimeType || mime || 'audio/webm' });
            const duration = window.__siState.recFinalDuration || siRecordingSeconds();
            siResetRecordingUi();
            if (blob.size < 1024 || duration < 1) {
                console.warn('voice recording too short, skipping upload');
                if (!window.__siState.recDiscarding) alert('Recording too short.');
                window.__siState.recDiscarding = false;
                return;
            }
            window.__siState.recDiscarding = false;
            siUploadVoice(blob, duration);
        };
        rec.start();
        window.__siState.recorder = rec;
        window.__siState.recStartedAt = Date.now();
        window.__siState.recPausedAt = 0;
        window.__siState.recPausedElapsed = 0;
        window.__siState.recFinalDuration = 0;
        window.__siState.recDiscarding = false;
        document.getElementById('siMicBtn')?.classList.add('recording');
        document.getElementById('siRecBar')?.classList.add('active');
        document.getElementById('siRecBar')?.classList.remove('paused');
        document.getElementById('siReplyInput')?.setAttribute('disabled', 'disabled');
        window.__siState.recTicker = setInterval(() => {
            const s = siRecordingSeconds();
            const el = document.getElementById('siRecTime');
            if (el) el.textContent = `${Math.floor(s/60)}:${String(s%60).padStart(2,'0')}`;
            if (s >= 300) siStopRecording();
        }, 250);
    } catch (err) {
        alert('Microphone access denied or unavailable.');
        console.warn(err);
    }
}

function siRecordingSeconds() {
    const state = window.__siState;
    if (!state.recStartedAt) return 0;
    const now = state.recPausedAt || Date.now();
    return Math.max(0, Math.floor((now - state.recStartedAt - (state.recPausedElapsed || 0)) / 1000));
}

function siTogglePauseRecording() {
    const state = window.__siState;
    const recorder = state.recorder;
    if (!recorder) return;

    const bar = document.getElementById('siRecBar');
    const button = document.getElementById('siRecPause');
    if (recorder.state === 'recording') {
        try { recorder.pause(); } catch (_) { return; }
        state.recPausedAt = Date.now();
        bar?.classList.add('paused');
        const label = document.getElementById('siRecLabel');
        if (label) label.textContent = 'Paused';
        if (button) {
            button.title = 'Resume recording';
            button.setAttribute('aria-label', 'Resume recording');
            button.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"></path></svg>';
        }
    } else if (recorder.state === 'paused') {
        try { recorder.resume(); } catch (_) { return; }
        state.recPausedElapsed += Date.now() - state.recPausedAt;
        state.recPausedAt = 0;
        bar?.classList.remove('paused');
        const label = document.getElementById('siRecLabel');
        if (label) label.textContent = 'Recording';
        if (button) {
            button.title = 'Pause recording';
            button.setAttribute('aria-label', 'Pause recording');
            button.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"></rect><rect x="14" y="5" width="4" height="14" rx="1"></rect></svg>';
        }
    }
}

function siRestartRecording() {
    if (!window.__siState.recorder) return;
    siCancelRecording();
    setTimeout(() => siStartRecording(), 80);
}

function siStopRecording() {
    const rec = window.__siState.recorder;
    if (!rec) return;
    window.__siState.recFinalDuration = siRecordingSeconds();
    clearInterval(window.__siState.recTicker);
    window.__siState.recTicker = null;
    try { rec.stop(); } catch (_) {}
    window.__siState.recorder = null;
}

function siCancelRecording() {
    const rec = window.__siState.recorder;
    if (!rec) return;
    window.__siState.recDiscarding = true;
    window.__siState.recFinalDuration = 0;
    clearInterval(window.__siState.recTicker);
    window.__siState.recTicker = null;
    rec.onstop = () => { try { rec.stream && rec.stream.getTracks?.().forEach(t => t.stop()); } catch (_) {} };
    try { rec.stop(); } catch (_) {}
    window.__siState.recorder = null;
    siResetRecordingUi();
}

function siResetRecordingUi() {
    document.getElementById('siMicBtn')?.classList.remove('recording');
    document.getElementById('siRecBar')?.classList.remove('active', 'paused');
    document.getElementById('siReplyInput')?.removeAttribute('disabled');
    const time = document.getElementById('siRecTime');
    if (time) time.textContent = '0:00';
    const label = document.getElementById('siRecLabel');
    if (label) label.textContent = 'Recording';
    const pause = document.getElementById('siRecPause');
    if (pause) {
        pause.title = 'Pause recording';
        pause.setAttribute('aria-label', 'Pause recording');
        pause.innerHTML = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"></rect><rect x="14" y="5" width="4" height="14" rx="1"></rect></svg>';
    }
}

async function siUploadVoice(blob, durationSec) {
    // Read the currently-selected conversation id from the Livewire state
    // (the admin side doesn't stash it on the input, so query the wire root).
    const root = document.querySelector('[wire\\:id]');
    const comp = root && Livewire.find(root.getAttribute('wire:id'));
    const conversationId = comp?.$get?.('selectedConversationId') ?? comp?.selectedConversationId;
    if (!conversationId) {
        alert('Select a conversation first.');
        return;
    }

    const type = blob.type && blob.type.includes('/') ? blob.type : 'audio/webm';
    const ext  = type.includes('mp4') ? 'm4a' : 'webm';
    const file = new File([blob], `voice-${Date.now()}.${ext}`, { type });

    const fd = new FormData();
    fd.append('conversation_id', conversationId);
    fd.append('voice_note', file);
    fd.append('duration', String(Math.max(1, durationSec)));

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    try {
        const res = await fetch('/chat/voice-upload', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: fd,
        });
        if (!res.ok) {
            const body = await res.text();
            console.error('[si-voice] upload HTTP', res.status, body);
            alert('Voice upload failed (' + res.status + ').');
            return;
        }
        comp?.call('refreshInbox');
    } catch (err) {
        console.error('[si-voice] upload error', err);
        alert('Voice upload failed. Check the console.');
    }
}

function siOpenLightbox(url) {
    let lb = document.getElementById('siLightbox');
    if (!lb) {
        lb = document.createElement('div');
        lb.id = 'siLightbox';
        lb.className = 'si-lightbox';
        lb.innerHTML = '<button class="close-btn">&times;</button><img alt="">';
        lb.addEventListener('click', (e) => {
            if (e.target === lb || e.target.classList.contains('close-btn')) lb.classList.remove('open');
        });
        document.body.appendChild(lb);
    }
    lb.querySelector('img').src = url;
    lb.classList.add('open');
}
window.siOpenLightbox = siOpenLightbox;

function siInit() {
    const indicator = document.getElementById('si-conn');
    const wait = setInterval(() => {
        if (!window.Echo) return;
        clearInterval(wait);

        const pusher = window.Echo.connector?.pusher;
        if (pusher) {
            const setOnline = () => indicator?.classList.replace('offline', 'online');
            const setOffline = () => indicator?.classList.replace('online', 'offline');
            pusher.connection.bind('connected', setOnline);
            pusher.connection.bind('disconnected', setOffline);
            if (pusher.connection.state === 'connected') setOnline();
        }

        try {
            window.Echo.private('support-inbox')
                .listen('.NewChatMessage', () => {
                    const root = document.querySelector('[wire\\:id]');
                    if (root) {
                        const id = root.getAttribute('wire:id');
                        Livewire.find(id).call('refreshInbox');
                        Livewire.find(id).$refresh();
                        siScrollBottom();
                    }
                });
        } catch (err) { console.warn('support-inbox subscribe failed', err); }
    }, 100);
    setTimeout(() => clearInterval(wait), 5000);
    siScrollBottom();

    // Socket fallback, while Reverb isn't connected (stops once it is):
    // every second, ask whether any support message changed (one cache read
    // on the server, App\Support\SupportInboxRevision) and run the full
    // refresh only when it did, so a customer's message shows in ~1 s.
    if (!window.__siState.revPoll) {
        window.__siState.revPoll = setInterval(siCheckRevision, 1000);
    }
    // Safety net for anything the revision doesn't track (call log, "5 min ago").
    if (!window.__siState.fallbackPoll) {
        window.__siState.fallbackPoll = setInterval(() => {
            if (document.hidden || siSocketUp()) return;
            siPollInboxIfIdle();
        }, 15000);
    }

    siWireTyping();
}

function siSocketUp() {
    return window.Echo?.connector?.pusher?.connection?.state === 'connected';
}

function siCheckRevision() {
    const s = window.__siState;
    const wrap = document.querySelector('.s-modern .si-wrap[data-inbox-rev]');
    if (!wrap || document.hidden || siSocketUp() || s.revBusy) return;
    // A refresh is already on its way; let it land before asking again.
    if (s.revRefreshAt && Date.now() - s.revRefreshAt < 2500) return;
    s.revBusy = true;
    fetch('/admin/support-inbox/rev', { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } })
        .then((r) => r.ok ? r.json() : null)
        .then((data) => {
            // The page shows the revision it was rendered at; a different one
            // on the server means something changed since.
            if (!data || String(data.rev) === wrap.dataset.inboxRev) return;
            if (siPollInboxIfIdle()) s.revRefreshAt = Date.now();
            // Not now (half-written reply or an open dialog): next second's check retries.
        })
        .catch(() => {})
        .finally(() => { s.revBusy = false; });
}

// Skipped while a form field, dialog, voice recording or upload is in
// progress, since a refresh would reset it. The reply box is the exception:
// tested safe (text and focus survive), so new messages show while the admin
// is mid-reply. Returns whether it refreshed.
function siPollInboxIfIdle() {
    const active = document.activeElement;
    if (active?.matches?.('input, select, textarea') && active.id !== 'siReplyInput') return false;
    if (document.querySelector('.si-modal.open, .si-stp-overlay, .si-qr-menu.open, .si-msg-actions.open, .si-row-actions.open, .si-head-more.open')) return false;
    if (window.__siState.recorder || document.querySelector('#siMsgs .si-pending-msg, #siAttachPreview.open')) return false;
    const comp = siComponent();
    const tab = comp?.$get?.('inboxTab') ?? comp?.inboxTab;
    if (!comp || (tab !== 'chat' && tab !== 'calls')) return false;
    comp.call('pollInbox');
    return true;
}

// WhatsApp-style "typing…" both ways (partials/_chat-typing): the admin's
// typing shows in the customer's Support chat, the customer's here.
function siWireTyping() {
    if (!window.EvTyping) return;
    if (!window.__siState.typingWired) {
        window.__siState.typingWired = true;
        document.addEventListener('input', (e) => {
            const input = e.target.closest?.('#siReplyInput');
            if (input) EvTyping.input(input.getAttribute('data-conversation-id'), input.value);
        });
        document.addEventListener('submit', (e) => {
            if (e.target.querySelector?.('#siReplyInput')) EvTyping.reset();
        }, true);
    }
    EvTyping.watch({
        convId: () => document.getElementById('siReplyInput')?.getAttribute('data-conversation-id'),
        row: () => document.getElementById('siTyping'),
        scroller: () => document.getElementById('siMsgs'),
        // They stopped typing — most likely they just sent: check now rather
        // than at the next tick (same check, so it never refreshes twice).
        onStop: () => siCheckRevision(),
    });
}

function siScrollBottom() {
    setTimeout(() => {
        const el = document.getElementById('siMsgs');
        if (el) el.scrollTop = el.scrollHeight;
    }, 80);
}

// Phones show one pane at a time (.is-detail on .si-wrap). Flip it on tap and
// show a spinner (.is-opening) over the pane; the server render that follows
// sets the class itself, which drops the spinner. If that render never lands,
// fall back to the list rather than leave a spinner with no way back.
function siSetPane(open) {
    const wrap = document.querySelector('.s-modern .si-wrap');
    if (!wrap || !window.matchMedia('(max-width: 767px)').matches) return;
    wrap.classList.toggle('is-detail', open);
    wrap.classList.toggle('is-opening', open);
    clearTimeout(window.__siState.paneTimer);
    if (!open) return;
    window.__siState.paneTimer = setTimeout(() => {
        const current = document.querySelector('.s-modern .si-wrap.is-opening');
        current?.classList.remove('is-opening', 'is-detail');
    }, 10000);
}

// Phones: size the full-screen inbox to the visible area so the composer sits
// above the on-screen keyboard. The vars live on <html>, outside the Livewire
// tree, so re-renders can't morph them away; the CSS only reads them on phones.
function siTrackViewport() {
    const vv = window.visualViewport;
    if (!vv || window.__siState.viewportTracked) return;
    window.__siState.viewportTracked = true;
    const root = document.documentElement;
    const apply = () => {
        // Pinch-zoomed: let the page zoom normally instead of shrinking the shell.
        if (vv.scale > 1.01) { root.classList.remove('si-vv'); return; }
        // Keep the latest message in view when the keyboard opens.
        const msgs = document.getElementById('siMsgs');
        const pinned = msgs && msgs.scrollHeight - msgs.scrollTop - msgs.clientHeight < 120;
        root.style.setProperty('--si-vv-top', vv.offsetTop + 'px');
        root.style.setProperty('--si-vv-height', vv.height + 'px');
        root.classList.add('si-vv');
        if (pinned) requestAnimationFrame(() => { msgs.scrollTop = msgs.scrollHeight; });
    };
    vv.addEventListener('resize', apply);
    vv.addEventListener('scroll', apply);
    apply();
}
</script>
@endpush
</div>
