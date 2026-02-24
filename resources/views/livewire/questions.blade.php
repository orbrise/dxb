<div>
    @section('headerform')
    <div class="nav-bar navbar-top-nav">
        <div class="container-fluid"> 
            <a class="back-link" href="{{url('/')}}" wire:navigate>
                <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Back</span></a>
            <div class="title">
                <h1><a href="/my-account">My Questions</a></h1>
            </div>
        </div>
    </div>
    @endsection

    <style>
        /* Stats Cards */
        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            flex: 1;
            background: #2a2a2a;
            border-radius: 8px;
            padding: 15px 20px;
            text-align: center;
            border: 1px solid #333;
        }

        .stat-card h3 {
            font-size: 28px;
            margin: 0 0 5px 0;
            color: #fff;
        }

        .stat-card h3.text-warning { color: #f4b827; }
        .stat-card h3.text-success { color: #28a745; }

        .stat-card p {
            color: #888;
            margin: 0;
            font-size: 14px;
        }

        /* Questions Container */
        .questions-container {
            display: flex;
            height: calc(100vh - 280px);
            min-height: 400px;
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #333;
        }

        /* Left Panel - Questions List */
        .questions-sidebar {
            width: 400px;
            min-width: 350px;
            background: #2a2a2a;
            border-right: 1px solid #333;
            display: flex;
            flex-direction: column;
        }

        .questions-sidebar-header {
            padding: 15px;
            background: #333;
            border-bottom: 1px solid #444;
        }

        .questions-sidebar-header h4 {
            color: #f4b827;
            margin: 0 0 10px 0;
            font-size: 18px;
        }

        .questions-search {
            display: flex;
            gap: 10px;
        }

        .questions-search input {
            flex: 1;
            padding: 10px 15px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 20px;
            color: #fff;
            font-size: 14px;
        }

        .questions-search input:focus {
            outline: none;
            border-color: #f4b827;
        }

        .questions-search input::placeholder {
            color: #888;
        }

        .questions-filter {
            padding: 8px 35px 8px 15px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 20px;
            color: #fff;
            font-size: 13px;
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 10px;
        }

        .questions-filter:focus {
            outline: none;
            border-color: #f4b827;
        }

        /* Questions List */
        .questions-list {
            flex: 1;
            overflow-y: auto;
        }

        .question-item {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            cursor: pointer;
            border-bottom: 1px solid #333;
            transition: background 0.2s;
        }

        .question-item:hover {
            background: #333;
        }

        .question-item.active {
            background: #3d3d3d;
            border-left: 3px solid #f4b827;
        }

        .question-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f4b827, #d4a017);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .question-info {
            flex: 1;
            min-width: 0;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .question-from {
            color: #fff;
            font-weight: 600;
            font-size: 14px;
        }

        .question-time {
            color: #888;
            font-size: 11px;
        }

        .question-item.unanswered .question-time {
            color: #f4b827;
        }

        .question-preview {
            color: #aaa;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .question-profile {
            color: #888;
            font-size: 11px;
            margin-top: 3px;
        }

        .unanswered-badge {
            background: #f4b827;
            color: #1a1a1a;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 8px;
            margin-left: 8px;
        }

        /* Right Panel - Answer Area */
        .question-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #1a1a1a;
        }

        .question-detail-header {
            padding: 15px 20px;
            background: #333;
            border-bottom: 1px solid #444;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .question-detail-info {
            display: flex;
            align-items: center;
        }

        .question-detail-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f4b827, #d4a017);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin-right: 12px;
        }

        .question-detail-name {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
        }

        .question-detail-profile {
            color: #888;
            font-size: 13px;
        }

        .question-detail-actions button {
            background: none;
            border: none;
            color: #888;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            margin-left: 5px;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .question-detail-actions button:hover {
            color: #f4b827;
            background: rgba(244, 184, 39, 0.1);
        }

        .question-detail-actions button.delete:hover {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
        }

        /* Q&A Area */
        .qa-area {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23222222' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .qa-bubble {
            max-width: 75%;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            position: relative;
        }

        .qa-question {
            background: #2a2a2a;
            color: #fff;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }

        .qa-answer {
            background: #f4b827;
            color: #1a1a1a;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .qa-label {
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 5px;
            opacity: 0.7;
        }

        .qa-text {
            font-size: 14px;
            line-height: 1.5;
        }

        .qa-time {
            font-size: 11px;
            opacity: 0.7;
            text-align: right;
            margin-top: 8px;
        }

        /* Answer Input */
        .answer-input {
            padding: 15px 20px;
            background: #2a2a2a;
            border-top: 1px solid #333;
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .answer-input textarea {
            flex: 1;
            padding: 12px 20px;
            background: #1a1a1a;
            border: 1px solid #444;
            border-radius: 20px;
            color: #fff;
            font-size: 14px;
            resize: none;
            max-height: 120px;
            min-height: 45px;
        }

        .answer-input textarea:focus {
            outline: none;
            border-color: #f4b827;
        }

        .answer-input textarea::placeholder {
            color: #888;
        }

        .answer-input button {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #f4b827;
            border: none;
            color: #1a1a1a;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .answer-input button:hover {
            background: #d4a017;
            transform: scale(1.05);
        }

        /* Empty State */
        .question-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #666;
            text-align: center;
            padding: 40px;
        }

        .question-empty i {
            font-size: 80px;
            margin-bottom: 20px;
            color: #444;
        }

        .question-empty h3 {
            color: #888;
            margin-bottom: 10px;
        }

        .question-empty p {
            color: #666;
            max-width: 300px;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .questions-container {
                height: calc(100vh - 180px);
                flex-direction: column;
            }

            .questions-sidebar {
                width: 100%;
                min-width: 100%;
                height: 100%;
                max-height: 100%;
                border-right: none;
                border-bottom: none;
                flex: 1;
            }

            .questions-sidebar.hidden {
                display: none;
            }

            .question-main {
                display: none;
                height: 100%;
            }

            .question-main.fullscreen {
                display: flex;
                height: 100%;
            }

            .mobile-back-btn {
                display: flex !important;
            }

            .qa-bubble {
                max-width: 90%;
            }

            .stats-row {
                flex-wrap: wrap;
            }

            .stat-card {
                flex: 1 1 calc(33.33% - 10px);
                min-width: 100px;
            }
        }

        /* Hide communication nav when question is selected on mobile */
        @media (max-width: 768px) {
            .hide-nav-mobile .communication-nav {
                display: none !important;
            }
        }

        .mobile-back-btn {
            display: none;
            background: #333;
            border: none;
            color: #f4b827;
            font-size: 20px;
            cursor: pointer;
            padding: 5px 10px;
        }

        /* Scrollbar styling */
        .questions-list::-webkit-scrollbar,
        .qa-area::-webkit-scrollbar {
            width: 6px;
        }

        .questions-list::-webkit-scrollbar-track,
        .qa-area::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .questions-list::-webkit-scrollbar-thumb,
        .qa-area::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 3px;
        }
    </style>

    <div class="container-fluid {{ $selectedQuestion ? 'hide-nav-mobile' : '' }}">
        <div class="content-wrapper no-sidebar">
            <div id="content">
                @include('components.communication-nav')
                
                <div class="mb-3" id="my-questions">
                    {{-- Flash Messages --}}
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif

                    {{-- Statistics Cards --}}
                    {{-- <div class="stats-row">
                        <div class="stat-card">
                            <h3>{{ $totalQuestions }}</h3>
                            <p>Total Questions</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="text-warning">{{ $unansweredCount }}</h3>
                            <p>Unanswered</p>
                        </div>
                        <div class="stat-card">
                            <h3 class="text-success">{{ $answeredCount }}</h3>
                            <p>Answered</p>
                        </div>
                    </div> --}}

                    {{-- WhatsApp-style Questions Container --}}
                    <div class="questions-container">
                        {{-- Left Sidebar - Questions List --}}
                        <div class="questions-sidebar {{ $selectedQuestion ? 'hidden' : '' }}" id="questionsSidebar">
                            <div class="questions-sidebar-header">
                                <h4><i class="fa fa-question-circle"></i> Questions</h4>
                                <div class="questions-search">
                                    <input 
                                        type="text" 
                                        placeholder="Search questions..." 
                                        wire:model.live.debounce.300ms="searchTerm">
                                    <select class="questions-filter" wire:model.live="filterStatus">
                                        <option value="all">All</option>
                                        <option value="unanswered">Unanswered</option>
                                        <option value="answered">Answered</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="questions-list">
                                @forelse($questions as $question)
                                    <div 
                                        class="question-item {{ $selectedQuestion && $selectedQuestion->id === $question->id ? 'active' : '' }} {{ !$question->answer ? 'unanswered' : '' }}"
                                        wire:click="selectQuestion({{ $question->id }})"
                                    >
                                        <div class="question-avatar">
                                            {{ strtoupper(substr($question->askedBy->name ?? $question->askedBy->email ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="question-info">
                                            <div class="question-header">
                                                <span class="question-from">
                                                    {{ $question->askedBy ? ($question->askedBy->name ?? $question->askedBy->email) : 'Guest' }}
                                                    @if(!$question->answer)
                                                        <span class="unanswered-badge">NEW</span>
                                                    @endif
                                                </span>
                                                <span class="question-time">{{ $question->created_at->diffForHumans(null, true) }}</span>
                                            </div>
                                            <div class="question-preview">{{ Str::limit($question->question, 50) }}</div>
                                            @if($question->profile)
                                                <div class="question-profile">To: {{ $question->profile->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="question-empty" style="padding: 40px 20px;">
                                        <i class="fa fa-question-circle"></i>
                                        <h3>No questions</h3>
                                        <p>You haven't received any questions yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Right Panel - Question Detail & Answer --}}
                        <div class="question-main {{ $selectedQuestion ? 'fullscreen' : '' }}">
                            @if($selectedQuestion)
                                {{-- Question Header --}}
                                <div class="question-detail-header">
                                    <div class="question-detail-info">
                                        <button class="mobile-back-btn" wire:click="closeModal">
                                            <i class="fa fa-arrow-left"></i>
                                        </button>
                                        <div class="question-detail-avatar">
                                            {{ strtoupper(substr($selectedQuestion->askedBy->name ?? $selectedQuestion->askedBy->email ?? 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="question-detail-name">{{ $selectedQuestion->askedBy ? ($selectedQuestion->askedBy->name ?? $selectedQuestion->askedBy->email) : 'Guest' }}</div>
                                            @if($selectedQuestion->profile)
                                                <div class="question-detail-profile">To: {{ $selectedQuestion->profile->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="question-detail-actions">
                                        <button wire:click="deleteQuestion({{ $selectedQuestion->id }})" class="delete" onclick="return confirm('Delete this question?')" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        <button wire:click="closeModal" title="Close">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Q&A Area --}}
                                <div class="qa-area">
                                    {{-- Question Bubble --}}
                                    <div class="qa-bubble qa-question">
                                        <div class="qa-label"><i class="fa fa-question-circle"></i> Question</div>
                                        <div class="qa-text">{{ $selectedQuestion->question }}</div>
                                        <div class="qa-time">{{ $selectedQuestion->created_at->format('M d, Y h:i A') }}</div>
                                    </div>

                                    {{-- Answer Bubble (if exists) --}}
                                    @if($selectedQuestion->answer)
                                        <div class="qa-bubble qa-answer">
                                            <div class="qa-label"><i class="fa fa-check-circle"></i> Your Answer</div>
                                            <div class="qa-text">{{ $selectedQuestion->answer }}</div>
                                            <div class="qa-time">{{ $selectedQuestion->updated_at->format('M d, Y h:i A') }}</div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Answer Input --}}
                                <form wire:submit.prevent="sendAnswer" class="answer-input">
                                    <textarea 
                                        wire:model="answer" 
                                        placeholder="{{ $selectedQuestion->answer ? 'Update your answer...' : 'Type your answer...' }}"
                                        rows="1"
                                    ></textarea>
                                    <button type="submit" title="Send Answer">
                                        <i class="fa fa-paper-plane"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Empty state when no question selected --}}
                                <div class="question-empty">
                                    <i class="fa fa-question-circle"></i>
                                    <h3>Select a question</h3>
                                    <p>Choose a question from the left to view and answer</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Pagination --}}
                    @if($questions->hasPages())
                    <div class="mt-3">
                        {{ $questions->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function scrollToAnswer() {
            setTimeout(() => {
                const qaArea = document.querySelector('.qa-area');
                if (qaArea) {
                    qaArea.scrollTop = qaArea.scrollHeight;
                }
            }, 100);
        }
    </script>
</div>
