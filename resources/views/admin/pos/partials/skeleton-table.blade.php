{{-- Skeleton Loading for Tables --}}
@for($i = 0; $i < ($count ?? 5); $i++)
  <tr>
    @for($j = 0; $j < ($columns ?? 5); $j++)
      <td>
        <div class="skeleton skeleton-text" style="width: {{ rand(60, 100) }}%"></div>
      </td>
    @endfor
  </tr>
@endfor
