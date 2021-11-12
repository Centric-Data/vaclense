<div class="vacancy__box">
  <style scoped>
  .vacancy__box{
    display: grid;
          grid-template-columns: max-content 1fr;
          grid-row-gap: 10px;
          grid-column-gap: 20px;
  }
  p{
    display: contents;
  }
  </style>
  <p>
   <label for="vacancy_org">Organisation</label>
   <input type="text" name="vacancy_org" id="vacancy_org" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'vacancy_org', true ) ); ?>">
 </p>
 <p>
   <label for="vacancy_pay">Renumeration</label>
   <input type="text" name="vacancy_pay" id="vacancy_pay" value="<?php echo esc_attr( get_post_meta( get_the_ID(), 'vacancy_pay', true ) ); ?>">
 </p>
</div>
