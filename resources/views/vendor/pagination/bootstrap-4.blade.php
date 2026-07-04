@if ($paginator->hasPages())
    <nav class="pagination epagination-2">
        <ul class="pagination epagination-2">
            {{-- Previous Page Link --}}
            @if (!$paginator->onFirstPage())
                <li class="page-item">
                    <a class="page-link" href="{{ \Request::url() }}" rel="prev" aria-label="@lang('pagination.first')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 15 15" fill="none">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M6.85355 3.85355C7.04882 3.65829 7.04882 3.34171 6.85355 3.14645C6.65829 2.95118 6.34171 2.95118 6.14645 3.14645L2.14645 7.14645C1.95118 7.34171 1.95118 7.65829 2.14645 7.85355L6.14645 11.8536C6.34171 12.0488 6.65829 12.0488 6.85355 11.8536C7.04882 11.6583 7.04882 11.3417 6.85355 11.1464L3.20711 7.5L6.85355 3.85355ZM12.8536 3.85355C13.0488 3.65829 13.0488 3.34171 12.8536 3.14645C12.6583 2.95118 12.3417 2.95118 12.1464 3.14645L8.14645 7.14645C7.95118 7.34171 7.95118 7.65829 8.14645 7.85355L12.1464 11.8536C12.3417 12.0488 12.6583 12.0488 12.8536 11.8536C13.0488 11.6583 13.0488 11.3417 12.8536 11.1464L9.20711 7.5L12.8536 3.85355Z" fill="#797c8b"/>
                        </svg>
                    </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          width="6"
                          height="12"
                          viewBox="0 0 8.414 14.828"
                        >
                          <path
                            id="Path_460"
                            data-name="Path 460"
                            d="M15,6,9,12l6,6"
                            transform="translate(-8 -4.586)"
                            fill="none"
                            stroke="#797c8b"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                          />
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item" aria-current="page"><span class="page-link active">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                  <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="6"
                      height="12"
                      viewBox="0 0 8.414 14.828"
                    >
                      <path
                        id="Path_462"
                        data-name="Path 462"
                        d="M9,6l6,6L9,18"
                        transform="translate(-7.586 -4.586)"
                        fill="none"
                        stroke="#797c8b"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                      />
                    </svg>
                  </a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="{{ \Request::url().'?page='.$paginator->lastPage() }}" rel="last" aria-label="@lang('pagination.last')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 15 15" fill="none">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M2.14645 11.1464C1.95118 11.3417 1.95118 11.6583 2.14645 11.8536C2.34171 12.0488 2.65829 12.0488 2.85355 11.8536L6.85355 7.85355C7.04882 7.65829 7.04882 7.34171 6.85355 7.14645L2.85355 3.14645C2.65829 2.95118 2.34171 2.95118 2.14645 3.14645C1.95118 3.34171 1.95118 3.65829 2.14645 3.85355L5.79289 7.5L2.14645 11.1464ZM8.14645 11.1464C7.95118 11.3417 7.95118 11.6583 8.14645 11.8536C8.34171 12.0488 8.65829 12.0488 8.85355 11.8536L12.8536 7.85355C13.0488 7.65829 13.0488 7.34171 12.8536 7.14645L8.85355 3.14645C8.65829 2.95118 8.34171 2.95118 8.14645 3.14645C7.95118 3.34171 7.95118 3.65829 8.14645 3.85355L11.7929 7.5L8.14645 11.1464Z" fill="#797c8b"/>
                        </svg>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif
