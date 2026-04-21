@if ($paginator->hasPages())
<div class="pagination-row">
  @if ($paginator->onFirstPage())
    <span class="btn-page disabled">‹</span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" class="btn-page">‹</a>
  @endif

  @foreach ($elements as $element)
    @if (is_string($element))
      <span class="btn-page disabled">…</span>
    @endif
    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span class="btn-page active">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="btn-page">{{ $page }}</a>
        @endif
      @endforeach
    @endif
  @endforeach

  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="btn-page">›</a>
  @else
    <span class="btn-page disabled">›</span>
  @endif

  <span class="pagination-info">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} di {{ $paginator->total() }}</span>
</div>
@endif